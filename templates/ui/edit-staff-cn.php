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
    <label style="margin-bottom: 5px; margin-top: 5px; float: left;" for="cn_name">Name in Native Language</label><input type="text" name="cn_name" id="cn_name" value="<?php echo esc_attr($profile->cn_name) ?>"/>
    <br/>
    <label for="sex">Gender/Sex*</label>
    <input type="radio" name="sex" value="M" id="M" required <?php if ($profile->sex == 'M') {echo 'checked';} ?>/>M
    <input type="radio" name="sex" value="F" id="F" required <?php if ($profile->sex == 'F') {echo 'checked';} ?>/>F
    <br/><br>
    <label for="age">Age*</label>
    <select id="age" name="age" required >
        <option value="18-25" <?php if ($profile->age == '18-25') {echo 'selected';} ?>>18-25</option>
        <option value="26-35" <?php if ($profile->age == '26-35') {echo 'selected';} ?>>26-35</option>
        <option value="36-45" <?php if ($profile->age == '36-45') {echo 'selected';} ?>>36-45</option>
        <option value="45+" <?php if ($profile->age == '45+') {echo 'selected';} ?>>45 or above</option>
    </select>
    <br/><br>
    <label for="email">Email*</label> <input type="email" name="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required value="<?php echo esc_attr($profile->email) ?>"/>
    <br/>
    <label for="phone">Phone*</label> <input type="tel" name="phone" id="phone" placeholder="Numbers Only!" required value="<?php echo esc_attr($profile->phone) ?>"/>
    <br/>
    <br><hr>
    <strong>Part 2: Current LC Service</strong><br><br>
    <label for="position">Position*</label>
    <select id="position" name="position" required>
        <option value="Administrator" <?php if ($profile->pos == 'Administrator') {echo 'selected';} ?>>Administrator</option>
        <option value="Principal" <?php if ($profile->pos == 'Principal') {echo 'selected';} ?>>Principal</option>
        <option value="Supervisor" <?php if ($profile->pos == 'Supervisor') {echo 'selected';} ?>>Supervisor</option>
        <option value="Monitor" <?php if ($profile->pos == 'Monitor') {echo 'selected';} ?>>Monitor</option>
        <option value="Others" <?php if ($profile->pos == 'Others') {echo 'selected';} ?>>Others</option>
    </select>
    <br/><br>
    <label for="lc">Learning Center*</label>
    <select id="lc" name="lc" required>
        <option value="Kindergarten" <?php if ($profile->lc == 'Kindergarten') {echo 'selected';} ?>>Kindergarten</option>
        <option value="Lower LC" <?php if ($profile->lc == 'Lower LC') {echo 'selected';} ?>>Lower LC</option>
        <option value="Upper LC" <?php if ($profile->lc == 'Upper LC') {echo 'selected';} ?>>Upper LC</option>
        <option value="Upper LC" <?php if ($profile->lc == 'Not in LC') {echo 'selected';} ?>>Not in LC</option>
    </select>
    <br />
    <br><hr>
    <strong>Part 3: Training Experience</strong><br><br>
    <label style="margin-top: 5px; margin-bottom: 5px; float: left;" for="t-exp">Number of Summer Training Attended*</label><input type="number" name="t-exp" id="t-exp" min="0" required value="<?php echo esc_attr($profile->training_exp) ?>"/>
    <br />
    <label style="margin-top: 5px; margin-bottom: 5px; float: left;" for="cec-exp">Number of Educators' Conventions Attended*</label><input type="number" name="cec-exp" id="cec-exp" min="0" required value="<?php echo esc_attr($profile->cec_exp) ?>"/>
    <br />
    <br><hr>
    <strong>Part 4: Educational Attainment</strong><br><br>
    <label style="margin-top: 5px; margin-bottom: 5px; margin-right: 15px; float: left;" for="degree">Highest Degree Obtained*</label>
    <select id="degree" name="degree" required>
        <option value="Elementary" <?php if ($profile->degree == 'Elementary') {echo 'selected';} ?>>Elementary</option>
        <option value="Middle School" <?php if ($profile->degree == 'Middle School') {echo 'selected';} ?>>Middle School</option>
        <option value="High School" <?php if ($profile->degree == 'High School') {echo 'selected';} ?>>High School</option>
        <option value="Associates" <?php if ($profile->degree == 'Associates') {echo 'selected';} ?>>Associates</option>
        <option value="Bachelors" <?php if ($profile->degree == 'Bachelors') {echo 'selected';} ?>>Bachelors</option>
        <option value="Masters" <?php if ($profile->degree == 'Masters') {echo 'selected';} ?>>Masters</option>
        <option value="Ph.D" <?php if ($profile->degree == 'Doctorate') {echo 'selected';} ?>>Doctorate</option>
    </select>
    <br/><br>
    <label for="grad-year">Year of Graduation*</label><input type="number" name="grad-year" id="grad-year" min="1950" required value="<?php echo esc_attr($profile->grad_year) ?>"/>
    <br />
    <label for="major">Major</label><input class="er_input" type="text" name="major" id="major" value="<?php echo esc_attr($profile->major); ?>"/>
    <br />
    <label for="minor">Minor</label><input class="er_input" type="text" name="minor" id="minor" value="<?php echo esc_attr($profile->minor); ?>"/>
    <br />
    <label for="institution">Institution*</label><input class="er_input" type="text" name="institution" id="institution" required value="<?php echo esc_attr($profile->institution); ?>"/>
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
