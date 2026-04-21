<?php

namespace App\Enums;

enum CandidateStatus: string
{
    case NEEDS_REVIEW = 'needs_review';
    case INVITE_SENT = 'invite_sent';
    case NO_RESPONSE = 'no_response';
    case INTERVIEW_SCHEDULED = 'interview_scheduled';
    case POST_INTERVIEW_REVIEW = 'post_interview_review';
    case PRE_SCREENING_PASSED = 'pre_screening_passed';
    case AWAITING_BACKGROUND_CHECK = 'awaiting_background_check';
    case OFFER_SENT = 'offer_sent';
    case OFFER_ACCEPTED = 'offer_accepted';
    case REJECTED = 'rejected';
    case APPLICANT_DECLINED = 'applicant_declined';
    case QUEUE = 'queue';
    case ONBOARDING = 'onboarding';
    case HIRED = 'hired';

    public function label(): string
    {
        return match ($this) {
            self::NEEDS_REVIEW             => 'Needs Review',
            self::INVITE_SENT              => 'Invite Sent',
            self::NO_RESPONSE              => 'No Response',
            self::INTERVIEW_SCHEDULED      => 'Interview Scheduled',
            self::POST_INTERVIEW_REVIEW    => 'Post-Interview (Application Pending)',
            self::PRE_SCREENING_PASSED     => 'Pre-Screening Passed',
            self::AWAITING_BACKGROUND_CHECK => 'Awaiting Background Check',
            self::OFFER_SENT               => 'Offer Sent',
            self::OFFER_ACCEPTED           => 'Offer Accepted',
            self::REJECTED                 => 'Rejected',
            self::APPLICANT_DECLINED       => 'Applicant Declined',
            self::QUEUE                    => 'Queue',
            self::ONBOARDING               => 'Onboarding',
            self::HIRED                    => 'Hired',
        };
    }
}
