<?php

namespace App\Policies;

use App\Models\Result;
use App\Models\User;

class ResultPolicy
{
    /**
     * Lecturers can only edit their own draft or HOD-rejected results.
     */
    public function update(User $user, Result $result): bool
    {
        return $user->isLecturer()
            && $result->lecturer_id === $user->id
            && in_array($result->status, [Result::STATUS_DRAFT, Result::STATUS_HOD_REJECTED]);
    }

    /**
     * Lecturers can submit their own draft or rejected results.
     */
    public function submit(User $user, Result $result): bool
    {
        return $user->isLecturer()
            && $result->lecturer_id === $user->id
            && $result->canBeSubmitted();
    }

    /**
     * HOD can action results from their own department only.
     */
    public function hodAction(User $user, Result $result): bool
    {
        return $user->isHod()
            && $result->course->department_id === $user->department_id
            && $result->canHodAction();
    }

    /**
     * Registrar can compile any HOD-approved result.
     */
    public function compile(User $user, Result $result): bool
    {
        return $user->isRegistrar() && $result->canBeCompiled();
    }

    /**
     * Senate can action any compiled result.
     */
    public function senateAction(User $user, Result $result): bool
    {
        return $user->isSenate() && $result->canSenateAction();
    }

    /**
     * Registrar/Admin can publish senate-approved results.
     */
    public function publish(User $user, Result $result): bool
    {
        return ($user->isRegistrar() || $user->isAdmin()) && $result->canBePublished();
    }

    /**
     * Students can view only their own published results AND must be finance-cleared.
     * This enforces automatic verification with finance department for pending dues.
     */
    public function view(User $user, Result $result): bool
    {
        // Must be the student who owns the result
        if ($user->isStudent() && $result->student_id === $user->id && $result->isPublished()) {
            // Automatically check with finance for pending dues
            $financeClearance = $user->financeClearance()
                ->where('academic_session_id', $result->academic_session_id)
                ->first();
            
            // Grant access only if finance cleared (no pending dues)
            return $financeClearance && $financeClearance->is_cleared === true;
        }
        
        return false;
    }

    /**
     * Lecturers can only delete their own draft results.
     */
    public function delete(User $user, Result $result): bool
    {
        return $user->isLecturer()
            && $result->lecturer_id === $user->id
            && $result->status === Result::STATUS_DRAFT;
    }
}
