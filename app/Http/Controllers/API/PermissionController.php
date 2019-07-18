<?php

namespace App\Http\Controllers\Api;

use App\Libraries\Permissions;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{

    public function permissionCheck()
    {
        $controller = new Permissions;

        return $controller;
    }

    public function serviceManagePermission()
    {
        if ($this->permissionCheck()->hasPermission('can_add_service') || $this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    public function bookingManagePermission()
    {
        if ($this->permissionCheck()->hasPermission('can_manage_booking') || $this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    public function bookingAddPermission()
    {
        if ($this->permissionCheck()->hasPermission('can_add_booking') || $this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    public function roleManagePermission()
    {
        if ($this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    public function userManagePermission()
    {
        if ($this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    public function appsManagePermission()
    {
        if ($this->permissionCheck()->hasPermission('can_edit_application_setting') || $this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    public function emailsManagePermission()
    {
        if ($this->permissionCheck()->hasPermission('can_edit_email_setting') || $this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    public function offdaysManagePermission()
    {
        if ($this->permissionCheck()->hasPermission('can_edit_off_day_setting') || $this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    public function holidaysManagePermission()
    {
        if ($this->permissionCheck()->hasPermission('can_edit_holi_day_setting') || $this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    public function etemplateManagePermission()
    {
        if ($this->permissionCheck()->hasPermission('can_edit_email_template') || $this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    // Client Settings Permission
    public function clientSettingsPermission()
    {
        if ($this->permissionCheck()->hasPermission('can_manage_client_setting') || $this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    // Client Manage Permission
    public function clientManagePermission()
    {
        if ($this->permissionCheck()->hasPermission('can_manage_client_setting') || $this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    // Report Permission
    public function reportPermission()
    {
        if ($this->permissionCheck()->hasPermission('can_see_reports') || $this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    // Payment Permission
    public function paymentSettingsPermission()
    {
        if ($this->permissionCheck()->hasPermission('can_manage_payment_methods') || $this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }

    // Update Permission
    public function updatePermission()
    {
        if ($this->permissionCheck()->isAdmin()) {

            return 1;

        } else {

            return 0;
        }
    }
}
