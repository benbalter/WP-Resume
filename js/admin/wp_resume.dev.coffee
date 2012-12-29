class WP_Resume
  
  constructor: ->
    switch pagenow
     
      # Options Page
      when "wp_resume_position_page_wp_resume_options"
        jQuery("#wp_resume_help, #wp_resume_clearfix, #multiple, .underHood").hide()
        jQuery("#wp_resume_help_toggle").click @toggleHelp
        jQuery("#toggleMultiple").click @toggleMultiple
        jQuery("#toggleHood").click @toggleHood
        jQuery("#user").change jQuery(".button-primary").click
        jQuery("#add_contact_field").click @addContactField
        jQuery(".button-primary").click @submitOptions
        @addContactField()
        @makeSortable()
        
      # Edit Position Page
      when "wp_resume_position"
        jQuery("#publish").click @validatePosition
        @addTaxonomyBoxEvents taxonomy for taxonomy in ["wp_resume_section", "wp_resume_organization"]
     
      # Edit Organization Page
      when "edit-wp_resume_organization"
        jQuery("#parent, #tag-slug").parent().hide()
        jQuery("#tag-name").siblings("p").text wp_resume.orgName
        jQuery("#tag-description").attr("rows", "1").siblings("label").text("Location").siblings("p").text wp_resume.orgLoc
    
      # Edit Section Page
      when "edit-wp_resume_section"
        jQuery("#parent").parent().hide()
        jQuery("#tag-description, #tag-slug").parent().hide()
        
  toggleHelp: ->
    jQuery("#wp_resume_help, #wp_resume_clearfix").toggle "fast"
    if jQuery(this).text() is wp_resume.more
      jQuery(this).text wp_resume.less
    else
      jQuery(this).text wp_resume.more
    false
    
  #verify that position has a section    
  validatePosition: (e) ->
   return unless jQuery("input:radio[name=wp_resume_section]:checked").val() is ""
   e.preventDefault()
   e.stopPropagation()
   alert wp_resume.missingTaxMsg
   jQuery("#ajax-loading").hide()
   setTimeout "jQuery('#publish').removeClass('button-primary-disabled')", 1
   false
   
  toggleMultiple: ->  
    jQuery("#multiple").toggle "fast"
    if jQuery(this).text() is wp_resume.yes
      jQuery(this).text wp_resume.no
    else
      jQuery(this).text wp_resume.yes
    false
  
  toggleHood: ->
    jQuery('.underHood').toggle "fast"
    if jQuery(this).text() is wp_resume.hideAdv
      jQuery(this).text wp_resume.showAdv
    else
      jQuery(this).text wp_resume.hideAdv
    false   
    
  addContactField: ->
    jQuery("#contact_info").append jQuery(".contact_info_blank").html()
    jQuery(".contact_info_row:last").fadeIn()
    false  
  
  addTaxonomyBoxEvents: (taxonomy) ->
    jQuery("#add_" + taxonomy + "_toggle").live "click", ->
      type = jQuery(this).attr("id").replace("_toggle", "").replace("add_", "")
      jQuery("#add_" + type + "_div").toggle()

    jQuery("#add_" + taxonomy + "_button").live "click", (event) ->
      type = jQuery(this).attr("id").replace("_button", "").replace("add_", "")
      jQuery("#" + type + "-ajax-loading").show()
      jQuery.post "admin-ajax.php?action=add_" + type, jQuery("#new_" + type + ", #new_" + type + "_location, #_ajax_nonce-add-" + type + ", #post_ID").serialize(), (data) ->
        jQuery("#" + type + "div .inside").html data
      event.preventDefault()  
  
  makeSortable: ->
    jQuery("#sections, .positions, .organizations").sortable
      axis: "y"
      containment: "parent"
      opacity: .5
      update: ->
      placeholder: "placeholder"
      forcePlaceholderSize: "true"
    jQuery("#sections").disableSelection()

  submitOptions: ->
    jQuery(".section").each (i, section) ->
      jQuery("#wp_resume_form").append "<input type=\"hidden\" name=\"wp_resume_options[order][" + jQuery(this).attr("id") + "]\" value=\"" + i + "\">"
    
    jQuery(".position").each (i, position) ->
      jQuery("#wp_resume_form").append "<input type=\"hidden\" name=\"wp_resume_options[position_order][" + jQuery(this).attr("id") + "]\" value=\"" + i + "\">"
    
jQuery(document).ready ->
  window.resume = new WP_Resume()
  