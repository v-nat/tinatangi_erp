<?php

namespace App\Enums;

enum Level: string {
    case Staff = 'staff';
    case Supervisor = 'supervisor';
    case Manager = 'manager';
    case CEO = 'ceo';
}