<?php

namespace App\Enums;

enum CandidateTaskStatus: string
{
    case APPOINTMENT_KEPT                = 'appointment_kept';
    case CONSUMER_RESCHEDULED            = 'consumer_rescheduled';
    case CONSUMER_CANCELLED              = 'consumer_cancelled';
    case MEDICAID_INACTIVE_REFERRED_SC   = 'medicaid_inactive_referred_sc';
    case NEED_STATUS_UPDATE              = 'need_status_update';
    case NO_SHOW_NO_RESPONSES            = 'no_show_no_responses';
    case STAFF_CANCELLED                 = 'staff_cancelled';
    case STAFF_RESCHEDULED               = 'staff_rescheduled';

    public function label(): string
    {
        return match ($this) {
            self::APPOINTMENT_KEPT              => 'Appointment Kept',
            self::CONSUMER_RESCHEDULED          => 'Consumer- Rescheduled',
            self::CONSUMER_CANCELLED            => 'Consumer-Cancelled',
            self::MEDICAID_INACTIVE_REFERRED_SC => 'Medicaid Inactive- Referred to SC for assistances',
            self::NEED_STATUS_UPDATE            => 'Need status update',
            self::NO_SHOW_NO_RESPONSES          => 'No-Show- No responses',
            self::STAFF_CANCELLED               => 'Staff- Cancelled',
            self::STAFF_RESCHEDULED             => 'Staff-Rescheduled',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label()],
            self::cases()
        );
    }
}
