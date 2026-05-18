<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class HiringPositionDescriptionSeeder extends Seeder
{
    public function run(): void
    {
        $text = <<<'TXT'
Supports Coordination: Bachelor's in Human Services, QIDP, and Michigan Supports Coordinator training. Must have 1+ year of experience coordinating care plans, conducting assessments, and managing community resources for individuals with disabilities.

Supports Coordination Assistant: High School Diploma. Must have 1+ year of experience providing administrative support in a social services or healthcare setting, including data entry and client scheduling.

Psychological Evaluations: Must have at least 1 year of hands-on experience administering cognitive and adaptive testing (e.g., WISC, Vineland) specifically for ASD, ADHD, IDD, and Guardianship cases.

Psychotherapy: Active licensure (LMSW, LPC, TLLP, LLP, or LP) with 1+ year of clinical experience providing individual therapy, crisis intervention, and treatment planning.

Group Therapy: Active licensure (LMSW, LPC, TLLP, LLP, or LP) with 1+ year of experience facilitating therapeutic groups, managing group dynamics, and documenting group sessions.

Masters Level Clinician: Active Master's degree and licensure (LMSW, LPC, TLLP, LLP) with 1+ year of post-graduate clinical experience in assessment, diagnosis, and evidence-based treatment.

Office Assistant: High School Diploma. Must have 1+ year of experience in medical or social service office administration, including phone triage, filing, and front-desk operations.

Financial Management – Claims Specialist: Must have 1+ year of experience processing Medicaid/Medicare claims, handling billing denials, and reconciling accounts in a healthcare or human services environment.

Financial Management – CPA: Must hold an active CPA license with 1+ year of experience in financial auditing, budget management, and regulatory compliance for non-profit or healthcare organizations.

Building Maintenance: Must have 1+ year of experience performing general facility repairs, HVAC basics, plumbing, electrical troubleshooting, and preventative maintenance in a commercial or residential setting.

Communication Person: Must have 1+ year of experience in public relations, media outreach, or internal communications, with strong writing skills and experience managing social media or newsletters.

HR / Quality Assurance: Must have 1+ year of experience in human resources (recruiting, onboarding, compliance) and QA (auditing clinical records, ensuring regulatory adherence) within a healthcare or social service agency.
TXT;

        Setting::set('hiring_position_description', $text);
    }
}
