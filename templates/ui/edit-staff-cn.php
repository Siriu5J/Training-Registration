<?php
/**
 * @var object $profile
 * @var int $staff_id
 * @var string $username
 */
?>
<div class="sot-tr-container">
    <div class="sot-tr-form">
        <h2 class="sot-tr-center">Edit Staff Profile</h2>
        
        <form id="staff-profile" name="staff-profile" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>">
            <?php wp_nonce_field('create_staff_nonce', 'staff_nonce_field'); ?>
            
            <p>Fields marked with * are required.</p>

            <fieldset>
                <legend>Part 1: Personal Information</legend>
                
                <div class="sot-tr-form-group">
                    <label for="first_name">First Name*</label>
                    <input type="text" name="first_name" id="first_name" value="<?php echo esc_attr($profile->first_name) ?>" required/>
                </div>

                <div class="sot-tr-form-group">
                    <label for="last_name">Last Name*</label>
                    <input type="text" name="last_name" id="last_name" value="<?php echo esc_attr($profile->last_name) ?>" required/>
                </div>

                <div class="sot-tr-form-group">
                    <label for="cn_name">Name in Native Language (Optional)</label>
                    <input type="text" name="cn_name" id="cn_name" value="<?php echo esc_attr($profile->cn_name) ?>"/>
                </div>

                <div class="sot-tr-form-group">
                    <label>Sex*</label>
                    <div style="display: flex; gap: 1.5rem; margin-top: 0.25rem;">
                        <label style="font-weight: 400; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="radio" name="sex" value="M" id="M" required <?php checked($profile->sex, 'M'); ?>/> Male
                        </label>
                        <label style="font-weight: 400; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="radio" name="sex" value="F" id="F" required <?php checked($profile->sex, 'F'); ?>/> Female
                        </label>
                    </div>
                </div>

                <div class="sot-tr-form-group">
                    <label for="age">Age Range*</label>
                    <select id="age" name="age" required>
                        <option value="18-25" <?php selected($profile->age, '18-25'); ?>>18-25</option>
                        <option value="26-35" <?php selected($profile->age, '26-35'); ?>>26-35</option>
                        <option value="36-45" <?php selected($profile->age, '36-45'); ?>>36-45</option>
                        <option value="45+" <?php selected($profile->age, '45+'); ?>>45 or above</option>
                    </select>
                </div>

                <div class="sot-tr-form-group">
                    <label for="email">Email Address*</label>
                    <input type="email" name="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required value="<?php echo esc_attr($profile->email) ?>"/>
                </div>

                <div class="sot-tr-form-group">
                    <label for="phone">Phone Number*</label>
                    <input type="tel" name="phone" id="phone" placeholder="Numbers only" required value="<?php echo esc_attr($profile->phone) ?>"/>
                </div>
            </fieldset>

            <fieldset>
                <legend>Part 2: Current LC Service</legend>
                
                <div class="sot-tr-form-group">
                    <label for="position">Position*</label>
                    <select id="position" name="position" required>
                        <option value="Administrator" <?php selected($profile->pos, 'Administrator'); ?>>Administrator</option>
                        <option value="Principal" <?php selected($profile->pos, 'Principal'); ?>>Principal</option>
                        <option value="Supervisor" <?php selected($profile->pos, 'Supervisor'); ?>>Supervisor</option>
                        <option value="Monitor" <?php selected($profile->pos, 'Monitor'); ?>>Monitor</option>
                        <option value="Others" <?php selected($profile->pos, 'Others'); ?>>Others</option>
                    </select>
                </div>

                <div class="sot-tr-form-group">
                    <label for="lc">Learning Center*</label>
                    <select id="lc" name="lc" required>
                        <option value="Kindergarten" <?php selected($profile->lc, 'Kindergarten'); ?>>Kindergarten</option>
                        <option value="Lower LC" <?php selected($profile->lc, 'Lower LC'); ?>>Lower LC</option>
                        <option value="Upper LC" <?php selected($profile->lc, 'Upper LC'); ?>>Upper LC</option>
                        <option value="Not in LC" <?php selected($profile->lc, 'Not in LC'); ?>>Not in LC</option>
                    </select>
                </div>
            </fieldset>

            <fieldset>
                <legend>Part 3: Training Experience</legend>
                
                <div class="sot-tr-form-group">
                    <label for="t-exp">Number of Summer Trainings Attended*</label>
                    <input type="number" name="t-exp" id="t-exp" min="0" required value="<?php echo esc_attr($profile->training_exp) ?>"/>
                </div>

                <div class="sot-tr-form-group">
                    <label for="cec-exp">Number of Educators' Conventions Attended*</label>
                    <input type="number" name="cec-exp" id="cec-exp" min="0" required value="<?php echo esc_attr($profile->cec_exp) ?>"/>
                </div>
            </fieldset>

            <fieldset>
                <legend>Part 4: Educational Attainment</legend>
                
                <div class="sot-tr-form-group">
                    <label for="degree">Highest Degree Obtained*</label>
                    <select id="degree" name="degree" required>
                        <option value="Elementary" <?php selected($profile->degree, 'Elementary'); ?>>Elementary</option>
                        <option value="Middle School" <?php selected($profile->degree, 'Middle School'); ?>>Middle School</option>
                        <option value="High School" <?php selected($profile->degree, 'High School'); ?>>High School</option>
                        <option value="Associates" <?php selected($profile->degree, 'Associates'); ?>>Associates</option>
                        <option value="Bachelors" <?php selected($profile->degree, 'Bachelors'); ?>>Bachelors</option>
                        <option value="Masters" <?php selected($profile->degree, 'Masters'); ?>>Masters</option>
                        <option value="Ph.D" <?php selected($profile->degree, 'Doctorate'); ?>>Doctorate</option>
                    </select>
                </div>

                <div class="sot-tr-form-group">
                    <label for="grad-year">Year of Graduation*</label>
                    <input type="number" name="grad-year" id="grad-year" min="1950" required value="<?php echo esc_attr($profile->grad_year) ?>"/>
                </div>

                <div class="sot-tr-form-group">
                    <label for="major">Major (Optional)</label>
                    <input type="text" name="major" id="major" value="<?php echo esc_attr($profile->major); ?>"/>
                </div>

                <div class="sot-tr-form-group">
                    <label for="institution">Institution (Optional)</label>
                    <input type="text" name="institution" id="institution" value="<?php echo esc_attr($profile->institution); ?>"/>
                </div>
            </fieldset>

            <div class="sot-tr-form-group">
                <label for="comment">Additional Comments (Optional)</label>
                <textarea name="comment" id="comment" rows="4"><?php echo esc_textarea($profile->comment) ?></textarea>
            </div>

            <input type="hidden" name="school" value="<?php echo esc_attr($username); ?>">
            <input type="hidden" name="id" value="<?php echo esc_attr($staff_id) ?>">
            
            <div class="sot-tr-submit-row">
                <input type="submit" name="update-profile" id="update-profile" value="Update Profile" class="button button-primary" />
                <input type="submit" name="update-cancel" id="update-cancel" value="Cancel" class="button" />
            </div>
        </form>
    </div>
</div>
