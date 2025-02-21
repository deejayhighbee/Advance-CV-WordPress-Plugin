jQuery(document).ready(function($) {
    // Initialize datepicker on elements with class 'datepicker'
    $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
    
    // Experience Repeater
    $('#add_experience').on('click', function(e) {
        e.preventDefault();
        var index = $('#experience_wrapper .experience_item').length;
        var html = '<div class="experience_item">'+
            '<p><label>Job Title:</label><br/>'+
            '<input type="text" name="cv_data[experience]['+index+'][job_title]" style="width:100%;" /></p>'+
            '<p><label>Company:</label><br/>'+
            '<input type="text" name="cv_data[experience]['+index+'][company]" style="width:100%;" /></p>'+
            '<p><label>Location:</label><br/>'+
            '<input type="text" name="cv_data[experience]['+index+'][location]" style="width:100%;" /></p>'+
            '<p><label>Dates:</label><br/>'+
            '<input type="text" name="cv_data[experience]['+index+'][dates]" class="datepicker" style="width:100%;" /></p>'+
            '<p><label>Details (one per line):</label><br/>'+
            '<textarea name="cv_data[experience]['+index+'][details]" rows="3" style="width:100%;"></textarea></p>'+
            '<button class="remove_experience">Remove Experience</button>'+
            '<hr/>'+
            '</div>';
        $('#experience_wrapper').append(html);
        $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
    });
    $(document).on('click', '.remove_experience', function(e) {
        e.preventDefault();
        $(this).closest('.experience_item').remove();
    });
    
    // Education Repeater
    $('#add_education').on('click', function(e) {
        e.preventDefault();
        var index = $('#education_wrapper .education_item').length;
        var html = '<div class="education_item">'+
            '<p><label>Degree &amp; School:</label><br/>'+
            '<input type="text" name="cv_data[education]['+index+'][degree_school]" style="width:100%;" /></p>'+
            '<p><label>Dates:</label><br/>'+
            '<input type="text" name="cv_data[education]['+index+'][dates]" class="datepicker" style="width:100%;" /></p>'+
            '<button class="remove_education">Remove Education</button>'+
            '<hr/>'+
            '</div>';
        $('#education_wrapper').append(html);
        $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
    });
    $(document).on('click', '.remove_education', function(e) {
        e.preventDefault();
        $(this).closest('.education_item').remove();
    });
    
    // Professional Skills Repeater
    $('#add_professional_skill').on('click', function(e) {
        e.preventDefault();
        var index = $('#professional_skills_wrapper .professional_skill_item').length;
        var html = '<div class="professional_skill_item">'+
            '<p><label>Skill Name:</label><br/>'+
            '<input type="text" name="cv_data[professional_skills]['+index+'][name]" style="width:100%;" /></p>'+
            '<p><label>Proficiency (0-100):</label><br/>'+
            '<input type="number" name="cv_data[professional_skills]['+index+'][proficiency]" style="width:100%;" /></p>'+
            '<button class="remove_professional_skill">Remove Skill</button>'+
            '<hr/>'+
            '</div>';
        $('#professional_skills_wrapper').append(html);
    });
    $(document).on('click', '.remove_professional_skill', function(e) {
        e.preventDefault();
        $(this).closest('.professional_skill_item').remove();
    });
    
    // Software Skills Repeater
    $('#add_software_skill').on('click', function(e) {
        e.preventDefault();
        var index = $('#software_skills_wrapper .software_skill_item').length;
        var html = '<div class="software_skill_item">'+
            '<p><label>Skill Name:</label><br/>'+
            '<input type="text" name="cv_data[software_skills]['+index+'][name]" style="width:100%;" /></p>'+
            '<p><label>Proficiency (0-100):</label><br/>'+
            '<input type="number" name="cv_data[software_skills]['+index+'][proficiency]" style="width:100%;" /></p>'+
            '<button class="remove_software_skill">Remove Skill</button>'+
            '<hr/>'+
            '</div>';
        $('#software_skills_wrapper').append(html);
    });
    $(document).on('click', '.remove_software_skill', function(e) {
        e.preventDefault();
        $(this).closest('.software_skill_item').remove();
    });
    
    // Certifications Repeater
    $('#add_certification').on('click', function(e) {
        e.preventDefault();
        var index = $('#certifications_wrapper .certification_item').length;
        var html = '<div class="certification_item">'+
            '<p><label>Certification Name:</label><br/>'+
            '<input type="text" name="cv_data[certifications]['+index+'][name]" style="width:100%;" /></p>'+
            '<p><label>Institution:</label><br/>'+
            '<input type="text" name="cv_data[certifications]['+index+'][institution]" style="width:100%;" /></p>'+
            '<p><label>Date:</label><br/>'+
            '<input type="text" name="cv_data[certifications]['+index+'][date]" class="datepicker" style="width:100%;" /></p>'+
            '<button class="remove_certification">Remove Certification</button>'+
            '<hr/>'+
            '</div>';
        $('#certifications_wrapper').append(html);
        $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
    });
    $(document).on('click', '.remove_certification', function(e) {
        e.preventDefault();
        $(this).closest('.certification_item').remove();
    });
    
    // Follow Me Repeater
    $('#add_follow_me').on('click', function(e) {
        e.preventDefault();
        var index = $('#follow_me_wrapper .follow_me_item').length;
        var html = '<div class="follow_me_item">'+
            '<p><label>Font Awesome Icon Class (e.g., fab fa-twitter):</label><br/>'+
            '<input type="text" name="cv_data[follow_me]['+index+'][icon]" style="width:100%;" /></p>'+
            '<p><label>Profile URL:</label><br/>'+
            '<input type="text" name="cv_data[follow_me]['+index+'][url]" style="width:100%;" /></p>'+
            '<button class="remove_follow_me">Remove</button>'+
            '<hr/>'+
            '</div>';
        $('#follow_me_wrapper').append(html);
    });
    $(document).on('click', '.remove_follow_me', function(e) {
        e.preventDefault();
        $(this).closest('.follow_me_item').remove();
    });
    
    // Language Skills Repeater
    $('#add_language').on('click', function(e) {
        e.preventDefault();
        var index = $('#languages_wrapper .language_item').length;
        var html = '<div class="language_item">'+
            '<p><label>Language:</label><br/>'+
            '<input type="text" name="cv_data[languages]['+index+'][language]" style="width:100%;" /></p>'+
            '<p><label>Proficiency:</label><br/>'+
            '<input type="text" name="cv_data[languages]['+index+'][proficiency]" style="width:100%;" /></p>'+
            '<button class="remove_language">Remove Language</button>'+
            '<hr/>'+
            '</div>';
        $('#languages_wrapper').append(html);
    });
    $(document).on('click', '.remove_language', function(e) {
        e.preventDefault();
        $(this).closest('.language_item').remove();
    });
    
    // Interests Repeater
    $('#add_interest').on('click', function(e) {
        e.preventDefault();
        var index = $('#interests_wrapper .interest_item').length;
        var html = '<div class="interest_item">'+
            '<p><label>Font Awesome Icon Class (e.g., fas fa-music):</label><br/>'+
            '<input type="text" name="cv_data[interests]['+index+'][icon]" style="width:100%;" /></p>'+
            '<p><label>Label:</label><br/>'+
            '<input type="text" name="cv_data[interests]['+index+'][label]" style="width:100%;" /></p>'+
            '<button class="remove_interest">Remove Interest</button>'+
            '<hr/>'+
            '</div>';
        $('#interests_wrapper').append(html);
    });
    $(document).on('click', '.remove_interest', function(e) {
        e.preventDefault();
        $(this).closest('.interest_item').remove();
    });
});
