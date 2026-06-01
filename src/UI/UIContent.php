<?php

namespace SOT\TrainingRegistration\UI;

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
        return $this->render('ui/dashboard', array('stats' => $stats), $echo);
    }
}
