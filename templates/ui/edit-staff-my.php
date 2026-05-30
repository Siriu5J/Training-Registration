<?php
/**
 * @var object $profile
 * @var int $staff_id
 * @var string $username
 */
?>
<form id="staff-profile" name="staff-profile" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>">
    <?php wp_nonce_field('create_staff_nonce', 'staff_nonce_field'); ?>
    <br>
    <hr>
    <h3>Editing Staff Profile</h3>
    <p>Fill out all fields marked with *</p>
    <strong>Part 1: Personal Information</strong><br><br>
    <label for="first_name">First Name*</label><input type="text" name="first_name" id="first_name" value="<?php echo esc_attr($profile->first_name) ?>" required/>
    <br/>
    <label for="last_name">Last Name*</label><input type="text" name="last_name" id="last_name" value="<?php echo esc_attr($profile->last_name) ?>" required/>
    <br/>
    <label style="margin-bottom: 5px; margin-top: 5px; float: left;" for="cn_name">Religion</label><input type="text" name="cn_name" id="cn_name" value="<?php echo esc_attr($profile->cn_name) ?>"/>
    <br/>
    <label for="sex">Gender*</label>
    <input type="radio" name="sex" value="M" id="M" required <?php if ($profile->sex == 'M') {echo 'checked';} ?>/>M
    <input type="radio" name="sex" value="F" id="F" required <?php if ($profile->sex == 'F') {echo 'checked';} ?>/>F
    <br/>
    <br/>
    <!-- This is a customization for SOT MY. Using the cn_name field -->
    <label style="margin-bottom: 5px; margin-top: 5px; float: left;" for="cn_name">Religion*</label><input type="text" name="cn_name" id="cn_name" required value="<?php echo esc_attr($profile->cn_name) ?>"/>
    <br />
    <label style="margin-top: 5px; margin-bottom: 5px; margin-right: 15px; float: left;" for="degree">Highest Degree Obtained*</label>
    <select id="degree" name="degree" required>
        <option value="SPM/O-Level" <?php if ($profile->degree == 'SPM/O-Level') {echo 'selected';} ?>>SPM/O-Level</option>
        <option value="STPM/A-Level" <?php if ($profile->degree == 'STPM/A-Level') {echo 'selected';} ?>>STPM/A-Level</option>
        <option value="Diploma" <?php if ($profile->degree == 'Diploma') {echo 'selected';} ?>>Diploma</option>
        <option value="Associates" <?php if ($profile->degree == 'Associates') {echo 'selected';} ?>>Associates</option>
        <option value="Bachelor&#39s Degree" <?php if ($profile->degree == "Bachelor's Degree") {echo 'selected';} ?>>Bachelor's Degree</option>
        <option value="Masters&#39 Degree" <?php if ($profile->degree == "Masters' Degree") {echo 'selected';} ?>>Masters' Degree</option>
        <option value="PhD/Doctorate" <?php if ($profile->degree == 'PhD/Doctorate') {echo 'selected';} ?>>PhD/Doctorate</option>
    </select>
    <br />
    <label for="phone">Mobile Number*</label> <input type="tel" name="phone" id="phone" placeholder="Numbers Only!" required value="<?php echo esc_attr($profile->phone) ?>"/>
    <br/>
    <br><hr>
    <strong>Part 2: School Service</strong><br><br>
    <label for="position">Position*</label>
    <select id="position" name="position" required>
        <option value="Administrator" <?php if ($profile->pos == 'Administrator') {echo 'selected';} ?>>Administrator</option>
        <option value="Principal" <?php if ($profile->pos == 'Principal') {echo 'selected';} ?>>Principal</option>
        <option value="Supervisor" <?php if ($profile->pos == 'Supervisor') {echo 'selected';} ?>>Supervisor</option>
        <option value="Monitor" <?php if ($profile->pos == 'Monitor') {echo 'selected';} ?>>Monitor</option>
        <option value="Others" <?php if ($profile->pos == 'Others') {echo 'selected';} ?>>Others</option>
    </select>
    <br/><br>
    <label style="margin-top: 5px; margin-bottom: 5px; float: left;" for="t-exp">Year of Last Training*</label><input type="number" name="t-exp" id="t-exp" min="1950" required value="<?php echo esc_attr($profile->grad_year) ?>"/>

    <label for="lc">Which Training was attended above?*</label>
    <select id="lc" name="lc" required>
        <option value="Administrators' " <?php if ($profile->lc == "Administrators' ") {echo 'selected';} ?>>Administrators'</option>
        <option value="Supervisors' " <?php if ($profile->lc == "Supervisors' ") {echo 'selected';} ?>>Supervisors'</option>
    </select>
    <br /><br><hr>
    <label for="comment">Comment</label><textarea name="comment" id="comment" cols="45" rows="5"><?php echo esc_textarea($profile->comment) ?></textarea>
    <br />
    <input type="hidden" name="school" value="<?php echo esc_attr($username); ?>">
    <input type="hidden" name="id" value="<?php echo esc_attr($staff_id) ?>">
    <br/>
    <input type="submit" name="update-profile" id="update-profile" value="Update Profile" />
    <input type="submit" name="update-cancel" id="update-cancel" value="Cancel" />
    <br />
</form>
