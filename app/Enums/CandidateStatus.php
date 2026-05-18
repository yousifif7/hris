<?php

namespace App\Enums;

enum CandidateStatus: string
{
    // ───────── Portal/workflow stages (existing; used by candidate-detail tabs) ─────────
    case HIRING                            = 'hiring';
    case PRE_SCREENING                     = 'pre_screening';
    case PRE_INTERVIEW_QUESTIONS           = 'pre_interview_questions';
    case VERIFICATION_AND_REVIEW           = 'verification_and_review';
    case OFFER_LETTER                      = 'offer_letter';
    case PRE_ONBOARD_DOCUMENTS             = 'pre_onboard_documents';
    case COMPLIANCE_AGREEMENTS             = 'compliance_agreements';
    case CLINICAL_STAFF_DOCUMENTS          = 'clinical_staff_documents';
    case EMERGENCY_CONTACT                 = 'emergency_contact';
    case TRAINING_AND_DEVELOPMENT          = 'training_and_development';
    case FINANCIAL_AND_PAYROLL_INFORMATION = 'financial_and_payroll_information';
    case POST_OFFER_DOCUMENTS              = 'post_offer_documents';
    case DWC_TRAININGS                     = 'dwc_trainings';
    case ADDITIONAL                        = 'additional';
    case JOB_DESCRIPTION_LETTER            = 'job_description_letter';

    // Terminal portal states
    case REJECTED                          = 'rejected';
    case APPLICANT_DECLINED                = 'applicant_declined';
    case HIRED                             = 'hired';

    // ───────── Workflow statuses (per HR Portal spec; used for automations) ─────────
    // These statuses describe the candidate independently of the portal tab they're on.
    case QUEUE                             = 'queue';
    case NEEDS_REVIEW                      = 'needs_review';
    case INVITE_SENT                       = 'invite_sent';
    case NO_RESPONSE_FOLLOWUP              = 'no_response_followup';
    case INTERVIEW_SCHEDULED               = 'interview_scheduled';
    case PRE_SCREENING_PASSED              = 'pre_screening_passed';
    case AWAITING_BACKGROUND_CHECK         = 'awaiting_background_check';
    case OFFER_SENT                        = 'offer_sent';
    case OFFER_ACCEPTED                    = 'offer_accepted';
    case OFFER_DECLINED                    = 'offer_declined';
    case PRE_ONBOARD                       = 'pre_onboard';
    case ACTIVE_STAFF                      = 'active_staff';
    case NOT_SELECTED                      = 'not_selected';

    public function label(): string
    {
        return match ($this) {
            self::HIRING                            => 'Hiring',
            self::PRE_SCREENING                     => 'Pre-Screening',
            self::PRE_INTERVIEW_QUESTIONS           => 'Pre-Interview Questions',
            self::VERIFICATION_AND_REVIEW           => 'Verification and Review',
            self::OFFER_LETTER                      => 'Offer Letter',
            self::PRE_ONBOARD_DOCUMENTS             => 'Pre-Onboard Documents',
            self::COMPLIANCE_AGREEMENTS             => 'Compliance Agreements',
            self::CLINICAL_STAFF_DOCUMENTS          => 'Clinical Staff Documents',
            self::EMERGENCY_CONTACT                 => 'Emergency Contact',
            self::TRAINING_AND_DEVELOPMENT          => 'Training and Development',
            self::FINANCIAL_AND_PAYROLL_INFORMATION => 'Financial and Payroll Information',
            self::POST_OFFER_DOCUMENTS              => 'Post-Offer Documents',
            self::DWC_TRAININGS                     => 'DWC Trainings',
            self::ADDITIONAL                        => 'Additional',
            self::JOB_DESCRIPTION_LETTER            => 'Job Description Letter',
            self::REJECTED                          => 'Rejected',
            self::APPLICANT_DECLINED                => 'Applicant Declined',
            self::HIRED                             => 'Hired',

            self::QUEUE                             => 'Queue',
            self::NEEDS_REVIEW                      => 'Needs Review',
            self::INVITE_SENT                       => 'Invite Sent',
            self::NO_RESPONSE_FOLLOWUP              => 'No Response Follow-up',
            self::INTERVIEW_SCHEDULED               => 'Interview Scheduled',
            self::PRE_SCREENING_PASSED              => 'Pre-Screening Passed',
            self::AWAITING_BACKGROUND_CHECK         => 'Awaiting Background Check',
            self::OFFER_SENT                        => 'Offer Sent',
            self::OFFER_ACCEPTED                    => 'Offer Accepted',
            self::OFFER_DECLINED                    => 'Offer Declined',
            self::PRE_ONBOARD                       => 'Pre-Onboard',
            self::ACTIVE_STAFF                      => 'Active Staff',
            self::NOT_SELECTED                      => 'Not Selected',
        };
    }

    /**
     * Ordered list of stages a candidate moves through (excludes terminals).
     * NOTE: This drives the candidate-detail tab order — leave the portal stages here.
     */
    public static function sequence(): array
    {
        return [
            self::HIRING,
            self::PRE_SCREENING,
            self::PRE_INTERVIEW_QUESTIONS,
            self::VERIFICATION_AND_REVIEW,
            self::OFFER_LETTER,
            self::PRE_ONBOARD_DOCUMENTS,
            self::COMPLIANCE_AGREEMENTS,
            self::CLINICAL_STAFF_DOCUMENTS,
            self::EMERGENCY_CONTACT,
            self::TRAINING_AND_DEVELOPMENT,
            self::FINANCIAL_AND_PAYROLL_INFORMATION,
            self::POST_OFFER_DOCUMENTS,
            self::DWC_TRAININGS,
            self::ADDITIONAL,
            self::JOB_DESCRIPTION_LETTER,
            self::HIRED,
        ];
    }

    /**
     * Workflow statuses surfaced in the candidate header status dropdown and the
     * Automations rule builder. These describe the *candidate* (independent of the
     * portal tab) and are the values that automations key off of.
     */
    public static function workflowStatuses(): array
    {
        return [
            self::QUEUE,
            self::NEEDS_REVIEW,
            self::INVITE_SENT,
            self::NO_RESPONSE_FOLLOWUP,
            self::INTERVIEW_SCHEDULED,
            self::PRE_SCREENING_PASSED,
            self::AWAITING_BACKGROUND_CHECK,
            self::OFFER_SENT,
            self::OFFER_ACCEPTED,
            self::OFFER_DECLINED,
            self::PRE_ONBOARD,
            self::ACTIVE_STAFF,
            self::NOT_SELECTED,
            self::APPLICANT_DECLINED,
        ];
    }

    public static function workflowOptions(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label()],
            self::workflowStatuses()
        );
    }
}
