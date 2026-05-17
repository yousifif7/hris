<?php

namespace App\Enums;

enum CandidateStatus: string
{
    // Recruitment & onboarding sequence — matches the candidate-edit tabs and the sidebar.
    case HIRING                            = 'hiring';                              // default entry: newly added, awaiting review
    case PRE_SCREENING                     = 'pre_screening';                       // invited to interview / interview booked
    case PRE_INTERVIEW_QUESTIONS           = 'pre_interview_questions';             // interviewed, awaiting post-interview application
    case VERIFICATION_AND_REVIEW           = 'verification_and_review';             // background checks + references
    case OFFER_LETTER                      = 'offer_letter';                        // offer prepared / sent
    case PRE_ONBOARD_DOCUMENTS             = 'pre_onboard_documents';               // offer accepted, base onboarding checklist
    case COMPLIANCE_AGREEMENTS             = 'compliance_agreements';
    case CLINICAL_STAFF_DOCUMENTS          = 'clinical_staff_documents';
    case EMERGENCY_CONTACT                 = 'emergency_contact';
    case TRAINING_AND_DEVELOPMENT          = 'training_and_development';
    case FINANCIAL_AND_PAYROLL_INFORMATION = 'financial_and_payroll_information';
    case POST_OFFER_DOCUMENTS              = 'post_offer_documents';
    case DWC_TRAININGS                     = 'dwc_trainings';
    case ADDITIONAL                        = 'additional';
    case JOB_DESCRIPTION_LETTER            = 'job_description_letter';

    // Terminal states (not in the visible sequence, but needed for filtering and conversions)
    case REJECTED                          = 'rejected';
    case APPLICANT_DECLINED                = 'applicant_declined';
    case HIRED                             = 'hired';

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
        };
    }

    /**
     * Ordered list of stages a candidate moves through (excludes terminals).
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
}
