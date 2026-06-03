<?php

namespace SOT\TrainingRegistration\UI;

defined('ABSPATH') || exit;

use SOT\TrainingRegistration\Traits\TemplateRenderer;

/**
 * Class UIContent
 *
 * This class handles rendering frontend pages using templates.
 *
 * @package SOT\TrainingRegistration\UI
 */
class UIContent {
    use TemplateRenderer;

    public function create_staff_cn($username, $echo = true) {
        return $this->render('ui/create-staff-cn', array('username' => $username, 'this_year' => date("Y")), $echo);
    }

    public function create_staff_my($username, $echo = true) {
        return $this->render('ui/create-staff-my', array('username' => $username, 'this_year' => date("Y")), $echo);
    }

    public function edit_staff_cn($username, $profile, $staff_id, $echo = true) {
        return $this->render('ui/edit-staff-cn', array(
            'username' => $username,
            'profile'  => $profile,
            'staff_id' => $staff_id
        ), $echo);
    }

    public function edit_staff_my($username, $profile, $staff_id, $echo = true) {
        return $this->render('ui/edit-staff-my', array(
            'username' => $username,
            'profile'  => $profile,
            'staff_id' => $staff_id
        ), $echo);
    }

    public function dashboard($stats, $echo = true) {
        $urls = array(
            'create_staff'         => get_permalink(get_option('er_create_staff_page_id')),
            'manage_staff'         => get_permalink(get_option('er_manage_staff_page_id')),
            'register_training'    => get_permalink(get_option('er_register_training_page_id')),
            'manage_registrations' => get_permalink(get_option('er_manage_registrations_page_id')),
        );

        return $this->render('ui/dashboard', array(
            'stats' => $stats,
            'urls'  => $urls
        ), $echo);
    }
}
