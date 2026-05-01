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
            'Collect and verify compliance agreements and acknowledgements.',
            ['Compliance Agreements']
        );
    }

    public function clinicalStaffDocument(): View
    {
        return $this->renderStage(
            'Clinical Staff Document',
            'Upload and validate clinical staff specific documentation.',
            ['Clinical Staff Document']
        );
    }

    public function emergencyContact(): View
    {
        return $this->renderStage(
            'Emergency Contact',
            'Ensure emergency contact details are collected and confirmed.',
            ['Emergency Contact']
        );
    }

    public function trainingDevelopment(): View
    {
        return $this->renderStage(
            'Training and Development',
            'Track training readiness and required development modules.',
            ['Training and Development']
        );
    }

    public function financialPayrollInformation(): View
    {
        return $this->renderStage(
            'Financial and Payroll Information',
            'Collect payroll setup forms and verify financial onboarding details.',
            ['Financial and Payroll Information']
        );
    }

    public function postOfferDocuments(): View
    {
        return $this->renderStage(
            'Post-offer Documents',
            'Track all post-offer documentation before activation.',
            ['Post-offer Documents']
        );
    }

    public function dwcTraining(): View
    {
        return $this->renderStage(
            'DWC Training',
            'Capture completion of DWC and related safety training items.',
            ['DWC Training']
        );
    }

    public function additional(): View
    {
        return $this->renderStage(
            'Additional',
            'Track any additional or site-specific onboarding requirements.',
            ['Additional']
        );
    }
}
