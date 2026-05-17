<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HrisWorkflowController extends Controller
{
    protected function renderStage(string $title, string $description, array $taskNames): View
    {
        return view('hris.workflow-stage', [
            'stageTitle' => $title,
            'stageDescription' => $description,
            'stageTaskNames' => $taskNames,
        ]);
    }

    public function preInterviewQuestions(): View
    {
        return $this->renderStage(
            'Pre-interview Questions',
            'Track candidate completion of pre-interview question packs before final review.',
            ['Pre-interview Questions']
        );
    }

    public function verificationsReview(): View
    {
        return $this->renderStage(
            'Verifications and Review',
            'Capture and confirm verification records before moving to offer and onboarding.',
            ['Verifications and Review']
        );
    }

    public function complianceAgreements(): View
    {
        return $this->renderStage(
            'Compliance Agreements',
            'Track onboarding compliance acknowledgements, policy sign-offs, and related confirmations.',
            ['Complete Background Check Consent', 'Review Employee Handbook']
        );
    }

    public function clinicalStaffDocument(): View
    {
        return $this->renderStage(
            'Clinical Staff Documents',
            'Manage licenses, credentials, and staff-specific documents needed before activation.',
            ['Upload Credentials & Licenses', 'Upload Driver\'s License']
        );
    }

    public function emergencyContact(): View
    {
        return $this->renderStage(
            'Emergency Contact',
            'Collect and confirm each employee\'s emergency contact details during onboarding.',
            ['Collect Emergency Contact Details']
        );
    }

    public function trainingDevelopment(): View
    {
        return view('hris.training-management', [
            'pageTitle' => 'Training and Development',
            'pageDescription' => 'Create and manage employee training items from a dedicated HR page.',
            'dwcOnly' => false,
        ]);
    }

    public function financialPayrollInformation(): View
    {
        return view('hris.payroll-management');
    }

    public function postOfferDocuments(): View
    {
        return $this->renderStage(
            'Post-offer Documents',
            'Finalize the HR documents that must be completed after the offer is accepted.',
            ['Complete I-9 Verification']
        );
    }

    public function dwcTraining(): View
    {
        return view('hris.training-management', [
            'pageTitle' => 'DWC Trainings',
            'pageDescription' => 'Manage DWC-specific training assignments and completion tracking.',
            'dwcOnly' => true,
        ]);
    }

    public function additional(): View
    {
        return $this->renderStage(
            'Additional',
            'Use this area for final setup tasks that do not belong to the other onboarding sections.',
            ['Select Orientation Date', 'Setup Email Account', 'Building Access & WiFi']
        );
    }
}
