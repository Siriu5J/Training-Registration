<?php
/**
 * @var object $profile
 * @var int $staff_id
 * @var string $username
 */
?>
<div class="sot-tr-container" id="edit-profile-view">
    <hr style="margin-bottom: 4rem; border-top: 2px solid #2271b1; opacity: 0.3;">
    
    <div class="sot-tr-form">
        <h2 class="sot-tr-center">Edit Staff Profile: <?php echo esc_html($profile->first_name . ' ' . $profile->last_name); ?></h2>
        
        <script>document.getElementById('edit-profile-view').scrollIntoView({ behavior: 'smooth' });</script>
        
        <form id="staff-profile" name="staff-profile" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>">
            <?php wp_nonce_field('create_staff_nonce', 'staff_nonce_field'); ?>
            
            <p>Fields marked with * are required.</p>

            <div class="sot-tr-card sot-tr-form-card">
                <h3>Part 1: Personal Information</h3>
                
                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label for="first_name">First Name*</label>
                        <input type="text" name="first_name" id="first_name" value="<?php echo esc_attr((string)$profile->first_name) ?>" required/>
                    </div>

                    <div class="sot-tr-form-group">
                        <label for="last_name">Last Name*</label>
                        <input type="text" name="last_name" id="last_name" value="<?php echo esc_attr((string)$profile->last_name) ?>" required/>
                    </div>
                </div>

                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label for="cn_name">Name in Native Language (Optional)</label>
                        <input type="text" name="cn_name" id="cn_name" value="<?php echo esc_attr((string)$profile->cn_name) ?>"/>
                    </div>

                    <div class="sot-tr-form-group">
                        <label>Sex*</label>
                        <div style="display: flex; gap: 1.5rem; margin-top: 0.25rem; height: 3rem; align-items: center;">
                            <label style="font-weight: 400; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" name="sex" value="M" id="M" required <?php checked($profile->sex, 'M'); ?>/> Male
                            </label>
                            <label style="font-weight: 400; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" name="sex" value="F" id="F" required <?php checked($profile->sex, 'F'); ?>/> Female
                            </label>
                        </div>
                    </div>
                </div>

                <div class="sot-tr-form-row">
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
                        <input type="email" name="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required value="<?php echo esc_attr((string)$profile->email) ?>"/>
                    </div>
                </div>

                <div class="sot-tr-form-group">
                    <label for="phone">Phone Number*</label>
                    <input type="tel" name="phone" id="phone" placeholder="Numbers only" required value="<?php echo esc_attr((string)$profile->phone) ?>"/>
                </div>
            </div>

            <div class="sot-tr-card sot-tr-form-card">
                <h3>Part 2: Current LC Service</h3>
                
                <div class="sot-tr-form-row">
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
                </div>
            </div>

            <div class="sot-tr-card sot-tr-form-card">
                <h3>Part 3: Training Experience</h3>
                
                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label for="t-exp">Summer Trainings Attended*</label>
                        <input type="number" name="t-exp" id="t-exp" min="0" required value="<?php echo esc_attr((string)$profile->training_exp) ?>"/>
                    </div>

                    <div class="sot-tr-form-group">
                        <label for="cec-exp">Educators' Conventions Attended*</label>
                        <input type="number" name="cec-exp" id="cec-exp" min="0" required value="<?php echo esc_attr((string)$profile->cec_exp) ?>"/>
                    </div>
                </div>
            </div>

            <div class="sot-tr-card sot-tr-form-card">
                <h3>Part 4: Educational Attainment</h3>
                
                <div class="sot-tr-form-row">
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
                        <input type="number" name="grad-year" id="grad-year" min="1950" required value="<?php echo esc_attr((string)$profile->grad_year) ?>"/>
                    </div>
                </div>

                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label for="major">Major (Optional)</label>
                        <input type="text" name="major" id="major" value="<?php echo esc_attr((string)$profile->major); ?>"/>
                    </div>

                    <div class="sot-tr-form-group">
                        <label for="institution">Institution (Optional)</label>
                        <input type="text" name="institution" id="institution" value="<?php echo esc_attr((string)$profile->institution); ?>"/>
                    </div>
                </div>
            </div>

            <div class="sot-tr-form-group">
                <label for="comment">Additional Comments (Optional)</label>
                <textarea name="comment" id="comment" rows="4"><?php echo esc_textarea((string)$profile->comment) ?></textarea>
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
