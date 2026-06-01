<?php
/**
 * @var object $profile
 * @var int $staff_id
 * @var string $username
 */
?>
<div class="sot-tr-container" id="edit-profile-view">
    <hr class="sot-tr-profile-hr">
    
    <div class="sot-tr-form">
        <h2 class="sot-tr-center">Edit Staff Profile: <?php echo esc_html($profile->first_name . ' ' . $profile->last_name); ?></h2>
        
        <script>document.getElementById('edit-profile-view').scrollIntoView({ behavior: 'smooth' });</script>
        
        <form id="staff-profile" name="staff-profile" method="post" action="<?php echo esc_url(add_query_arg(array()));?>">
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
                        <label>Gender*</label>
                        <div class="sot-tr-radio-group">
                            <label class="sot-tr-radio-label">
                                <input type="radio" name="sex" value="M" id="M" required <?php checked($profile->sex, 'M'); ?>/> Male
                            </label>
                            <label class="sot-tr-radio-label">
                                <input type="radio" name="sex" value="F" id="F" required <?php checked($profile->sex, 'F'); ?>/> Female
                            </label>
                        </div>
                    </div>

                    <div class="sot-tr-form-group">
                        <label for="cn_name">Religion*</label>
                        <input type="text" name="cn_name" id="cn_name" required value="<?php echo esc_attr((string)$profile->cn_name) ?>"/>
                    </div>
                </div>

                <div class="sot-tr-form-row">
                    <div class="sot-tr-form-group">
                        <label for="degree">Highest Education Level*</label>
                        <select id="degree" name="degree" required>
                            <option value="SPM/O-Level" <?php selected($profile->degree, 'SPM/O-Level'); ?>>SPM/O-Level</option>
                            <option value="STPM/A-Level" <?php selected($profile->degree, 'STPM/A-Level'); ?>>STPM/A-Level</option>
                            <option value="Diploma" <?php selected($profile->degree, 'Diploma'); ?>>Diploma</option>
                            <option value="Bachelor's Degree" <?php selected($profile->degree, "Bachelor's Degree"); ?>>Bachelor's Degree</option>
                            <option value="Masters' Degree" <?php selected($profile->degree, "Masters' Degree"); ?>>Masters' Degree</option>
                            <option value="PhD/Doctorate" <?php selected($profile->degree, 'PhD/Doctorate'); ?>>PhD/Doctorate</option>
                        </select>
                    </div>

                    <div class="sot-tr-form-group">
                        <label for="phone">Mobile Number*</label>
                        <input type="tel" name="phone" id="phone" placeholder="Numbers only" required value="<?php echo esc_attr((string)$profile->phone) ?>"/>
                    </div>
                </div>
            </div>

            <div class="sot-tr-card sot-tr-form-card">
                <h3>Part 2: School Service</h3>
                
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
                        <label for="t-exp">Year of Last Training*</label>
                        <input type="number" name="t-exp" id="t-exp" min="2010" required value="<?php echo esc_attr((string)$profile->grad_year) ?>"/>
                    </div>
                </div>

                <div class="sot-tr-form-group">
                    <label for="lc">Which training was attended above?*</label>
                    <select id="lc" name="lc" required>
                        <option value="Administrators' " <?php selected($profile->lc, "Administrators' "); ?>>Administrators'</option>
                        <option value="Supervisors' " <?php selected($profile->lc, "Supervisors' "); ?>>Supervisors'</option>
                    </select>
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
