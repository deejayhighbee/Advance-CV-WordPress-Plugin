<?php
/*
Plugin Name: Advanced CV Plugin
Plugin URI: https://ayodeji.co/advanced-cv-plugin
Description: Create and display multiple customizable CVs via a shortcode.
Version: 1.0
Author: Ayodeji Ibrahim Lateef
Author URI: https://ayodeji.co
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Advanced_CV_Plugin {
    
    public function __construct() {
        // Register custom post type.
        add_action('init', array($this, 'register_cv_post_type'));
        // Enqueue frontend assets.
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        // Enqueue admin assets.
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        // Register meta boxes.
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        // Save meta box data.
        add_action('save_post_cv', array($this, 'save_cv_meta'));
        // Register shortcode.
        add_shortcode('advanced_cv', array($this, 'cv_shortcode'));
        // AJAX for phone number retrieval.
        add_action('wp_ajax_get_phone_number', array($this, 'ajax_get_phone_number'));
        add_action('wp_ajax_nopriv_get_phone_number', array($this, 'ajax_get_phone_number'));
        // Admin settings.
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function register_cv_post_type() {
        $labels = array(
            'name'               => 'CVs',
            'singular_name'      => 'CV',
            'add_new'            => 'Add New CV',
            'add_new_item'       => 'Add New CV',
            'edit_item'          => 'Edit CV',
            'new_item'           => 'New CV',
            'view_item'          => 'View CV',
            'search_items'       => 'Search CVs',
            'not_found'          => 'No CVs found',
            'not_found_in_trash' => 'No CVs found in Trash',
            'menu_name'          => 'CVs'
        );

        $args = array(
            'labels'        => $labels,
            'public'        => false,
            'show_ui'       => true,
            'show_in_menu'  => true,
            'supports'      => array('title'), // Remove editor support.
            'has_archive'   => false,
            'rewrite'       => array('slug' => 'cv')
        );
        register_post_type('cv', $args);
    }
    
    public function enqueue_frontend_assets() {
        wp_enqueue_style('advanced-cv-style', plugin_dir_url(__FILE__) . 'assets/css/cv.css');
        wp_enqueue_script('advanced-cv-script', plugin_dir_url(__FILE__) . 'assets/js/cv.js', array('jquery'), '1.1', true);
        
        // Localize script with AJAX URL and nonce.
        wp_localize_script('advanced-cv-script', 'advancedCVVars', array(
            'ajax_url'   => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('cv_ajax_nonce')
        ));
        
        // Enqueue Font Awesome if the settings say to load it.
        $options = get_option('advanced_cv_options', array('load_font_awesome' => 1));
        if ( isset($options['load_font_awesome']) && $options['load_font_awesome'] ) {
            wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
        }
    }
    
    public function enqueue_admin_assets($hook) {
        global $post_type;
        if ( ('post.php' == $hook || 'post-new.php' == $hook) && $post_type == 'cv' ) {
            wp_enqueue_script('advanced-cv-admin', plugin_dir_url(__FILE__) . 'includes/admin.js', array('jquery', 'jquery-ui-datepicker'), '1.1', true);
            wp_enqueue_style('advanced-cv-admin-style', plugin_dir_url(__FILE__) . 'includes/admin.css');
            // Enqueue jQuery UI CSS from CDN for datepicker.
            wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
        }
    }
    
    public function register_meta_boxes() {
        add_meta_box('cv_meta_box', 'CV Details', array($this, 'render_meta_box'), 'cv', 'normal', 'high');
    }
    
    public function render_meta_box($post) {
        wp_nonce_field('save_cv_meta', 'cv_meta_nonce');
        $cv_data = get_post_meta($post->ID, 'cv_data', true);
        $cv_data = wp_parse_args($cv_data, array(
            'name'                  => '',
            'job_title'             => '',
            'date_of_birth'         => '',
            'profile_picture'       => '',
            'professional_summary'  => '',
            'contact_phone'         => '',
            'contact_email'         => '',
            'contact_address'       => '',
            'experience'            => array(),
            'education'             => array(),
            'professional_skills'   => array(),
            'software_skills'       => array(),
            'certifications'        => array(),
            'follow_me'             => array(),
            'languages'             => array(),
            'interests'             => array(),
        ));
        ?>
        <h3>Basic Information</h3>
        <p>
            <label for="cv_name">Name:</label><br/>
            <input type="text" id="cv_name" name="cv_data[name]" value="<?php echo esc_attr($cv_data['name']); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="cv_job_title">Job Title:</label><br/>
            <input type="text" id="cv_job_title" name="cv_data[job_title]" value="<?php echo esc_attr($cv_data['job_title']); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="cv_date_of_birth">Date of Birth:</label><br/>
            <input type="text" id="cv_date_of_birth" name="cv_data[date_of_birth]" class="datepicker" value="<?php echo esc_attr($cv_data['date_of_birth']); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="cv_profile_picture">Profile Picture URL:</label><br/>
            <input type="text" id="cv_profile_picture" name="cv_data[profile_picture]" value="<?php echo esc_attr($cv_data['profile_picture']); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="cv_professional_summary">Professional Summary:</label><br/>
            <textarea id="cv_professional_summary" name="cv_data[professional_summary]" rows="4" style="width:100%;"><?php echo esc_textarea($cv_data['professional_summary']); ?></textarea>
        </p>
        <h3>Contact Information</h3>
        <p>
            <label for="cv_contact_phone">Phone:</label><br/>
            <input type="text" id="cv_contact_phone" name="cv_data[contact_phone]" value="<?php echo esc_attr($cv_data['contact_phone']); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="cv_contact_email">Email:</label><br/>
            <input type="email" id="cv_contact_email" name="cv_data[contact_email]" value="<?php echo esc_attr($cv_data['contact_email']); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="cv_contact_address">Address:</label><br/>
            <input type="text" id="cv_contact_address" name="cv_data[contact_address]" value="<?php echo esc_attr($cv_data['contact_address']); ?>" style="width:100%;" />
        </p>
        
        <h3>Experience</h3>
        <div id="experience_wrapper">
            <?php 
            if (!empty($cv_data['experience']) && is_array($cv_data['experience'])) {
                foreach($cv_data['experience'] as $index => $exp) {
                    ?>
                    <div class="experience_item">
                        <p>
                            <label>Job Title:</label><br/>
                            <input type="text" name="cv_data[experience][<?php echo $index; ?>][job_title]" value="<?php echo esc_attr($exp['job_title']); ?>" style="width:100%;" />
                        </p>
                        <p>
                            <label>Company:</label><br/>
                            <input type="text" name="cv_data[experience][<?php echo $index; ?>][company]" value="<?php echo esc_attr($exp['company']); ?>" style="width:100%;" />
                        </p>
                        <p>
                            <label>Location:</label><br/>
                            <input type="text" name="cv_data[experience][<?php echo $index; ?>][location]" value="<?php echo esc_attr($exp['location']); ?>" style="width:100%;" />
                        </p>
                        <p>
                            <label>Dates:</label><br/>
                            <input type="text" name="cv_data[experience][<?php echo $index; ?>][dates]" class="datepicker" value="<?php echo esc_attr($exp['dates']); ?>" style="width:100%;" />
                        </p>
                        <p>
                            <label>Details (one per line):</label><br/>
                            <textarea name="cv_data[experience][<?php echo $index; ?>][details]" rows="3" style="width:100%;"><?php echo esc_textarea($exp['details']); ?></textarea>
                        </p>
                        <button class="remove_experience">Remove Experience</button>
                        <hr/>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <button id="add_experience">Add Experience</button>
        
        <h3>Education</h3>
        <div id="education_wrapper">
            <?php 
            if (!empty($cv_data['education']) && is_array($cv_data['education'])) {
                foreach($cv_data['education'] as $index => $edu) {
                    ?>
                    <div class="education_item">
                        <p>
                            <label>Degree &amp; School:</label><br/>
                            <input type="text" name="cv_data[education][<?php echo $index; ?>][degree_school]" value="<?php echo esc_attr($edu['degree_school']); ?>" style="width:100%;" />
                        </p>
                        <p>
                            <label>Dates:</label><br/>
                            <input type="text" name="cv_data[education][<?php echo $index; ?>][dates]" class="datepicker" value="<?php echo esc_attr($edu['dates']); ?>" style="width:100%;" />
                        </p>
                        <button class="remove_education">Remove Education</button>
                        <hr/>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <button id="add_education">Add Education</button>
        
        <h3>Professional Skills</h3>
        <div id="professional_skills_wrapper">
            <?php 
            if (!empty($cv_data['professional_skills']) && is_array($cv_data['professional_skills'])) {
                foreach($cv_data['professional_skills'] as $index => $skill) {
                    ?>
                    <div class="professional_skill_item">
                        <p>
                            <label>Skill Name:</label><br/>
                            <input type="text" name="cv_data[professional_skills][<?php echo $index; ?>][name]" value="<?php echo esc_attr($skill['name']); ?>" style="width:100%;" />
                        </p>
                        <p>
                            <label>Proficiency (0-100):</label><br/>
                            <input type="number" name="cv_data[professional_skills][<?php echo $index; ?>][proficiency]" value="<?php echo esc_attr($skill['proficiency']); ?>" style="width:100%;" />
                        </p>
                        <button class="remove_professional_skill">Remove Skill</button>
                        <hr/>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <button id="add_professional_skill">Add Professional Skill</button>
        
        <h3>Software Skills</h3>
        <div id="software_skills_wrapper">
            <?php 
            if (!empty($cv_data['software_skills']) && is_array($cv_data['software_skills'])) {
                foreach($cv_data['software_skills'] as $index => $skill) {
                    ?>
                    <div class="software_skill_item">
                        <p>
                            <label>Skill Name:</label><br/>
                            <input type="text" name="cv_data[software_skills][<?php echo $index; ?>][name]" value="<?php echo esc_attr($skill['name']); ?>" style="width:100%;" />
                        </p>
                        <p>
                            <label>Proficiency (0-100):</label><br/>
                            <input type="number" name="cv_data[software_skills][<?php echo $index; ?>][proficiency]" value="<?php echo esc_attr($skill['proficiency']); ?>" style="width:100%;" />
                        </p>
                        <button class="remove_software_skill">Remove Skill</button>
                        <hr/>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <button id="add_software_skill">Add Software Skill</button>
        
        <h3>Certifications</h3>
        <div id="certifications_wrapper">
            <?php 
            if (!empty($cv_data['certifications']) && is_array($cv_data['certifications'])) {
                foreach($cv_data['certifications'] as $index => $cert) {
                    ?>
                    <div class="certification_item">
                        <p>
                            <label>Certification Name:</label><br/>
                            <input type="text" name="cv_data[certifications][<?php echo $index; ?>][name]" value="<?php echo esc_attr($cert['name']); ?>" style="width:100%;" />
                        </p>
                        <p>
                            <label>Institution:</label><br/>
                            <input type="text" name="cv_data[certifications][<?php echo $index; ?>][institution]" value="<?php echo esc_attr($cert['institution']); ?>" style="width:100%;" />
                        </p>
                        <p>
                            <label>Date:</label><br/>
                            <input type="text" name="cv_data[certifications][<?php echo $index; ?>][date]" class="datepicker" value="<?php echo esc_attr($cert['date']); ?>" style="width:100%;" />
                        </p>
                        <button class="remove_certification">Remove Certification</button>
                        <hr/>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <button id="add_certification">Add Certification</button>
        
        <h3>Follow Me</h3>
        <div id="follow_me_wrapper">
            <?php 
            if (!empty($cv_data['follow_me']) && is_array($cv_data['follow_me'])) {
                foreach($cv_data['follow_me'] as $index => $follow) {
                    ?>
                    <div class="follow_me_item">
                        <p>
                            <label>Font Awesome Icon Class (e.g., fab fa-twitter):</label><br/>
                            <input type="text" name="cv_data[follow_me][<?php echo $index; ?>][icon]" value="<?php echo esc_attr($follow['icon']); ?>" style="width:100%;" />
                        </p>
                        <p>
                            <label>Profile URL:</label><br/>
                            <input type="text" name="cv_data[follow_me][<?php echo $index; ?>][url]" value="<?php echo esc_attr($follow['url']); ?>" style="width:100%;" />
                        </p>
                        <button class="remove_follow_me">Remove</button>
                        <hr/>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <button id="add_follow_me">Add Follow Profile</button>
        
        <h3>Language Skills</h3>
        <div id="languages_wrapper">
            <?php 
            if (!empty($cv_data['languages']) && is_array($cv_data['languages'])) {
                foreach($cv_data['languages'] as $index => $lang) {
                    ?>
                    <div class="language_item">
                        <p>
                            <label>Language:</label><br/>
                            <input type="text" name="cv_data[languages][<?php echo $index; ?>][language]" value="<?php echo esc_attr($lang['language']); ?>" style="width:100%;" />
                        </p>
                        <p>
                            <label>Proficiency:</label><br/>
                            <input type="text" name="cv_data[languages][<?php echo $index; ?>][proficiency]" value="<?php echo esc_attr($lang['proficiency']); ?>" style="width:100%;" />
                        </p>
                        <button class="remove_language">Remove Language</button>
                        <hr/>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <button id="add_language">Add Language</button>
        
        <h3>Interests</h3>
        <div id="interests_wrapper">
            <?php 
            if (!empty($cv_data['interests']) && is_array($cv_data['interests'])) {
                foreach($cv_data['interests'] as $index => $interest) {
                    ?>
                    <div class="interest_item">
                        <p>
                            <label>Font Awesome Icon Class (e.g., fas fa-music):</label><br/>
                            <input type="text" name="cv_data[interests][<?php echo $index; ?>][icon]" value="<?php echo esc_attr($interest['icon']); ?>" style="width:100%;" />
                        </p>
                        <p>
                            <label>Label:</label><br/>
                            <input type="text" name="cv_data[interests][<?php echo $index; ?>][label]" value="<?php echo esc_attr($interest['label']); ?>" style="width:100%;" />
                        </p>
                        <button class="remove_interest">Remove Interest</button>
                        <hr/>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <button id="add_interest">Add Interest</button>
        
        <?php
    }
    
    public function save_cv_meta($post_id) {
        if (!isset($_POST['cv_meta_nonce']) || !wp_verify_nonce($_POST['cv_meta_nonce'], 'save_cv_meta')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (isset($_POST['cv_data']) && is_array($_POST['cv_data'])) {
            update_post_meta($post_id, 'cv_data', $_POST['cv_data']);
        }
    }
    
    public function cv_shortcode($atts) {
        $atts = shortcode_atts(array('id' => ''), $atts, 'advanced_cv');
        if (empty($atts['id'])) {
            return 'No CV ID provided.';
        }
        $post = get_post($atts['id']);
        if (!$post || $post->post_type !== 'cv') {
            return 'CV not found.';
        }
        $cv_data = get_post_meta($post->ID, 'cv_data', true);
        ob_start();
        ?>
        <div class="resume">
            <div class="base">
                <div class="profile">
                    <div class="photo">
                        <img src="<?php echo esc_url($cv_data['profile_picture']); ?>" alt="<?php echo esc_attr($cv_data['name']); ?>" />
                    </div>
                    <div class="info">
                        <h1 class="name"><?php echo esc_html($cv_data['name']); ?></h1>
                        <h2 class="job"><?php echo esc_html($cv_data['job_title']); ?></h2>
                    </div>
                </div>
                <div class="about">
                    <h3>Professional Summary</h3>
                    <p><?php echo esc_html($cv_data['professional_summary']); ?></p>
                </div>
                <div class="contact">
                    <h3>Contact Me</h3>
                    <div class="call">
                        <a href="#" class="phoneLink" data-cv-id="<?php echo esc_attr($post->ID); ?>">
                            <i class="fas fa-phone"></i>
                            <span class="phone-number blurred"><?php echo esc_html($cv_data['contact_phone']); ?></span>
                            <button class="revealNumber">Reveal Phone Number</button>
                        </a>
                    </div>
                    <div class="address">
                        <a href="#"><i class="fas fa-map-marker"></i><span><?php echo esc_html($cv_data['contact_address']); ?></span></a>
                    </div>
                    <div class="email">
                        <a href="mailto:<?php echo esc_attr($cv_data['contact_email']); ?>"><i class="fas fa-envelope"></i><span><?php echo esc_html($cv_data['contact_email']); ?></span></a>
                    </div>
                    <?php if(!empty($cv_data['date_of_birth'])): ?>
                    <div class="dob"><i class="fas fa-birthday-cake"></i> <?php echo date('jS F, Y', strtotime($cv_data['date_of_birth'])); ?></div>
                    <?php endif; ?>
                </div>
                <div class="follow">
                    <h3>Follow Me</h3>
                    <div class="box">
                        <?php
                        if (!empty($cv_data['follow_me']) && is_array($cv_data['follow_me'])) {
                            foreach($cv_data['follow_me'] as $follow) {
                                if(!empty($follow['url'])) {
                                    echo '<a href="'.esc_url($follow['url']).'" target="_blank"><i class="'.esc_attr($follow['icon']).'"></i></a>';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="func">
                <?php if (!empty($cv_data['experience']) && is_array($cv_data['experience'])): ?>
                <div class="work">
                    <h3><i class="fa fa-briefcase"></i>Experience</h3>
                    <?php foreach ($cv_data['experience'] as $exp): ?>
                        <div class="job-item">
                            <div class="job-header">
                                <h4>
                                    <span class="job-title"><?php echo esc_html($exp['job_title']); ?></span><br>
                                    <span class="company"><?php echo esc_html($exp['company']); ?></span><br>
                                    <span class="location"><?php echo esc_html($exp['location']); ?></span>
                                </h4>
                                <span class="job-dates"><?php echo esc_html($exp['dates']); ?></span>
                                <button class="toggle-btn">View Details</button>
                            </div>
                            <div class="job-details collapsible-content">
                                <ul>
                                    <?php 
                                    $details = explode("\n", $exp['details']);
                                    foreach ($details as $detail) {
                                        if(trim($detail) !== '') {
                                            echo '<li>' . esc_html($detail) . '</li>';
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($cv_data['education']) && is_array($cv_data['education'])): ?>
                <div class="edu">
                    <h3><i class="fa fa-graduation-cap"></i>Education</h3>
                    <ul>
                        <?php foreach ($cv_data['education'] as $edu): ?>
                            <li>
                                <span><?php echo esc_html($edu['degree_school']); ?></span>
                                <small><?php echo esc_html($edu['dates']); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($cv_data['professional_skills']) && is_array($cv_data['professional_skills'])): ?>
                <div class="skills-prog">
                    <h3><i class="fas fa-code"></i>Professional Skills</h3>
                    <ul>
                        <?php foreach ($cv_data['professional_skills'] as $skill): ?>
                            <li data-percent="<?php echo esc_attr($skill['proficiency']); ?>">
                                <span><?php echo esc_html($skill['name']); ?></span>
                                <div class="skills-bar">
                                    <div class="bar"></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($cv_data['software_skills']) && is_array($cv_data['software_skills'])): ?>
                <div class="skills-soft">
                    <h3><i class="fas fa-bezier-curve"></i>Software Skills</h3>
                    <ul>
                        <?php foreach ($cv_data['software_skills'] as $skill): ?>
                            <li data-percent="<?php echo esc_attr($skill['proficiency']); ?>">
                                <svg viewbox="0 0 100 100">
                                    <circle cx="50" cy="50" r="45"></circle>
                                    <circle class="cbar" cx="50" cy="50" r="45"></circle>
                                </svg>
                                <span><?php echo esc_html($skill['name']); ?></span>
                                <small></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($cv_data['certifications']) && is_array($cv_data['certifications'])): ?>
                <div class="certifications">
                    <h3><i class="fas fa-certificate"></i>Certifications</h3>
                    <ul>
                        <?php foreach ($cv_data['certifications'] as $cert): ?>
                            <li>
                                <span><?php echo esc_html($cert['name']); ?></span> - 
                                <span><?php echo esc_html($cert['institution']); ?></span>
                                <small><?php echo esc_html($cert['date']); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($cv_data['languages']) && is_array($cv_data['languages'])): ?>
                <div class="languages">
                    <h3><i class="fas fa-language"></i>Language Skills</h3>
                    <ul>
                        <?php foreach ($cv_data['languages'] as $lang): ?>
                            <li><?php echo esc_html($lang['language']); ?>: <?php echo esc_html($lang['proficiency']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($cv_data['interests']) && is_array($cv_data['interests'])): ?>
                <div class="interests">
                    <h3><i class="fas fa-star"></i>Hobbies & Interests</h3>
                    <div class="interests-items">
                        <?php 
                        foreach ($cv_data['interests'] as $interest) {
                            if(!empty($interest['label'])) {
                                echo '<div><i class="'.esc_attr($interest['icon']).'"></i><span>' . esc_html($interest['label']) . '</span></div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function ajax_get_phone_number() {
        check_ajax_referer('cv_ajax_nonce', 'nonce');
        $cv_id = isset($_POST['cv_id']) ? intval($_POST['cv_id']) : 0;
        if (!$cv_id) {
            wp_send_json_error('Invalid CV ID.');
        }
        $cv_data = get_post_meta($cv_id, 'cv_data', true);
        $phone = isset($cv_data['contact_phone']) ? $cv_data['contact_phone'] : '';
        if (!$phone) {
            wp_send_json_error('Phone number not set.');
        }
        wp_send_json_success($phone);
    }
    
    public function register_settings_page() {
        add_options_page('Advanced CV Settings', 'Advanced CV Settings', 'manage_options', 'advanced-cv-settings', array($this, 'settings_page_html'));
    }
    
    public function register_settings() {
        register_setting('advanced_cv_settings', 'advanced_cv_options');
        add_settings_section('advanced_cv_main', 'Main Settings', null, 'advanced-cv-settings');
        add_settings_field('load_font_awesome', 'Load Font Awesome', array($this, 'load_font_awesome_field_html'), 'advanced-cv-settings', 'advanced_cv_main');
    }
    
    public function load_font_awesome_field_html() {
        $options = get_option('advanced_cv_options', array('load_font_awesome' => 1));
        ?>
        <input type="checkbox" name="advanced_cv_options[load_font_awesome]" value="1" <?php checked(1, isset($options['load_font_awesome']) ? $options['load_font_awesome'] : 0); ?> />
        <label for="advanced_cv_options[load_font_awesome]">Check if your theme does NOT already load Font Awesome</label>
        <?php
    }
    
    public function settings_page_html() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>Advanced CV Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('advanced_cv_settings');
                do_settings_sections('advanced-cv-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

new Advanced_CV_Plugin();