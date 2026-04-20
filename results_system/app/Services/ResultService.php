<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\FinanceClearance;
use App\Models\Result;
use App\Models\User;
use App\Notifications\ResultStatusNotification;
use Illuminate\Support\Facades\DB;

class ResultService
{
    // ── Lecturer: submit draft results ───────────────────────────
    public function submitResult(Result $result): Result
    {
        if (!$result->canBeSubmitted()) {
            throw new \Exception('Result cannot be submitted in its current state.');
        }

        DB::transaction(function () use ($result) {
            $old = $result->status;

            $result->update([
                'status'       => Result::STATUS_SUBMITTED,
                'submitted_at' => now(),
            ]);

            AuditLog::record('result.submitted', $result,
                ['status' => $old],
                ['status' => Result::STATUS_SUBMITTED]
            );
        });

        return $result->fresh();
    }

    // ── HOD: approve ─────────────────────────────────────────────
    public function hodApprove(Result $result, ?string $comment = null): Result
    {
        if (!$result->canHodAction()) {
            throw new \Exception('Result is not awaiting HOD review.');
        }

        DB::transaction(function () use ($result, $comment) {
            $old = $result->status;

            $result->update([
                'status'          => Result::STATUS_HOD_APPROVED,
                'hod_comment'     => $comment,
                'hod_actioned_at' => now(),
            ]);

            AuditLog::record('result.hod_approved', $result,
                ['status' => $old],
                ['status' => Result::STATUS_HOD_APPROVED, 'comment' => $comment]
            );
        });

        return $result->fresh();
    }

    // ── HOD: reject ──────────────────────────────────────────────
    public function hodReject(Result $result, string $comment): Result
    {
        if (!$result->canHodAction()) {
            throw new \Exception('Result is not awaiting HOD review.');
        }

        DB::transaction(function () use ($result, $comment) {
            $old = $result->status;

            $result->update([
                'status'          => Result::STATUS_HOD_REJECTED,
                'hod_comment'     => $comment,
                'hod_actioned_at' => now(),
            ]);

            AuditLog::record('result.hod_rejected', $result,
                ['status' => $old],
                ['status' => Result::STATUS_HOD_REJECTED, 'comment' => $comment]
            );

            // Notify lecturer
            $result->lecturer->notify(new ResultStatusNotification($result, 'rejected_by_hod'));
        });

        return $result->fresh();
    }

    // ── Registrar: compile (bulk HOD-approved → compiled) ────────
    public function compileResults(array $resultIds): int
    {
        $count = 0;

        DB::transaction(function () use ($resultIds, &$count) {
            $results = Result::whereIn('id', $resultIds)
                ->where('status', Result::STATUS_HOD_APPROVED)
                ->get();

            foreach ($results as $result) {
                $result->update([
                    'status'      => Result::STATUS_COMPILED,
                    'compiled_at' => now(),
                ]);

                AuditLog::record('result.compiled', $result,
                    ['status' => Result::STATUS_HOD_APPROVED],
                    ['status' => Result::STATUS_COMPILED]
                );

                $count++;
            }
        });

        return $count;
    }

    // ── Senate: approve (bulk) ────────────────────────────────────
    public function senateApprove(array $resultIds, ?string $comment = null): int
    {
        $count = 0;

        DB::transaction(function () use ($resultIds, $comment, &$count) {
            $results = Result::whereIn('id', $resultIds)
                ->where('status', Result::STATUS_COMPILED)
                ->get();

            foreach ($results as $result) {
                $result->update([
                    'status'             => Result::STATUS_SENATE_APPROVED,
                    'senate_comment'     => $comment,
                    'senate_actioned_at' => now(),
                ]);

                AuditLog::record('result.senate_approved', $result,
                    ['status' => Result::STATUS_COMPILED],
                    ['status' => Result::STATUS_SENATE_APPROVED]
                );

                $count++;
            }
        });

        return $count;
    }

    // ── Senate: reject (bulk back to compiled) ────────────────────
    public function senateReject(array $resultIds, string $comment): int
    {
        $count = 0;

        DB::transaction(function () use ($resultIds, $comment, &$count) {
            $results = Result::whereIn('id', $resultIds)
                ->where('status', Result::STATUS_COMPILED)
                ->get();

            foreach ($results as $result) {
                $result->update([
                    'status'             => Result::STATUS_SENATE_REJECTED,
                    'senate_comment'     => $comment,
                    'senate_actioned_at' => now(),
                ]);

                AuditLog::record('result.senate_rejected', $result,
                    ['status' => Result::STATUS_COMPILED],
                    ['status' => Result::STATUS_SENATE_REJECTED, 'comment' => $comment]
                );

                $count++;
            }
        });

        return $count;
    }

    // ── Registrar/Admin: publish ──────────────────────────────────
    public function publishResults(array $resultIds): int
    {
        $count = 0;

        DB::transaction(function () use ($resultIds, &$count) {
            $results = Result::whereIn('id', $resultIds)
                ->where('status', Result::STATUS_SENATE_APPROVED)
                ->get();

            foreach ($results as $result) {
                // Check clearances before publishing per student
                if (!$this->studentIsCleared($result->student_id, $result->academic_session_id)) {
                    continue; // skip students with outstanding clearances
                }

                $result->update([
                    'status'       => Result::STATUS_PUBLISHED,
                    'published_at' => now(),
                ]);

                AuditLog::record('result.published', $result,
                    ['status' => Result::STATUS_SENATE_APPROVED],
                    ['status' => Result::STATUS_PUBLISHED]
                );

                // Notify student
                $result->student->notify(new ResultStatusNotification($result, 'published'));

                $count++;
            }
        });

        return $count;
    }

    // ── Clearance check ───────────────────────────────────────────
    public function studentIsCleared(int $studentId, int $sessionId): bool
    {
        $finance = FinanceClearance::where('student_id', $studentId)
            ->where('academic_session_id', $sessionId)
            ->first();

        return $finance?->is_cleared;
    }

    // ── GPA calculation ───────────────────────────────────────────
    public function calculateGPA(int $studentId, int $sessionId): float
    {
        $results = Result::where('student_id', $studentId)
            ->where('academic_session_id', $sessionId)
            ->where('status', Result::STATUS_PUBLISHED)
            ->with('course')
            ->get();

        if ($results->isEmpty()) return 0.0;

        $totalPoints = 0;
        $totalUnits  = 0;

        foreach ($results as $result) {
            $units        = $result->course->credit_units ?? 3;
            $totalPoints += $result->grade_points * $units;
            $totalUnits  += $units;
        }

        return $totalUnits > 0 ? round($totalPoints / $totalUnits, 2) : 0.0;
    }
}
