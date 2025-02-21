jQuery(document).ready(function($) {
    // INITIAL ANIMATION for Professional Skills (formerly Programming Skills)
    $(".skills-prog li").find(".skills-bar").each(function(i) {
      $(this).find(".bar").delay(i * 150).animate(
        { width: $(this).closest('li').attr("data-percent") + "%" },
        1000,
        "linear",
        function() { $(this).css({ "transition-duration": ".5s" }); }
      );
    });
  
    // HOVER ANIMATION for Professional Skills:
    $(".skills-prog li").hover(
      function() {
        var $bar = $(this).find(".skills-bar .bar");
        $bar.css({
          "transition": "all 0.5s ease",
          "transform": "scale(1.1)",
          "background-color": "#fdd835",
          "height": "8px"
        });
      },
      function() {
        var $bar = $(this).find(".skills-bar .bar");
        $bar.css({
          "transition": "all 0.5s ease",
          "transform": "scale(1)",
          "background-color": "#ffb300",
          "height": "4px"
        });
      }
    );
  
    // ANIMATION for Software Skills (SVG circles)
    $(".skills-soft li").find("svg").each(function(i) {
      var c, cbar, circle, percent, r;
      circle = $(this).children(".cbar");
      r = circle.attr("r");
      c = Math.PI * (r * 2);
      percent = $(this).parent().data("percent");
      cbar = (100 - percent) / 100 * c;
      circle.css({ "stroke-dashoffset": c, "stroke-dasharray": c });
      circle.delay(i * 150).animate(
        { strokeDashoffset: cbar },
        1000,
        "linear",
        function() { circle.css({ "transition-duration": ".3s" }); }
      );
      $(this).siblings("small").prop("Counter", 0).delay(i * 150).animate(
        { Counter: percent },
        {
          duration: 1000,
          step: function(now) { $(this).text(Math.ceil(now) + "%"); }
        }
      );
    });
      
    // Reveal phone number on button click using AJAX.
    $('.revealNumber').click(function(e) {
      e.preventDefault();
      var $parent = $(this).closest('.phoneLink');
      var cv_id = $parent.data('cv-id');
      $.ajax({
        url: advancedCVVars.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'get_phone_number',
          cv_id: cv_id,
          nonce: advancedCVVars.ajax_nonce
        },
        success: function(response) {
          if(response.success) {
            $parent.find('.phone-number').text(response.data).removeClass('blurred');
            $parent.attr('href', 'tel:' + response.data.replace(/\D/g, ''));
            $parent.find('.revealNumber').hide();
          } else {
            alert('Error: ' + response.data);
          }
        },
        error: function() { alert('There was an error fetching the phone number.'); }
      });
    });
    
    // Toggle job details on click.
    $('.toggle-btn').click(function(){
      $(this).closest('.job-item').find('.collapsible-content').slideToggle();
    });
  });
  