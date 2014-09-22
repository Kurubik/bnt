$(->
  Popups.init()
)


Popups =
  init: ->
    $clickItem = $('.show_popup')
    self = @
    $closePopup = $('.close_popup')
    $confirmButton = $('#confirm_terms')
    @$popup = $('#popup')
    $('form').on 'submit', Validation.submit

    $clickItem.on 'click', (e) ->
      e.preventDefault()
      self.switchAction($(@))

    $closePopup.on 'click', (e) ->
      e.preventDefault()
      self.popupDispose()

    @$popup.on 'click', (e) ->
      if self.$popup.is(e.target)
        self.popupDispose()

    $confirmButton.on 'click', (e) ->
      e.preventDefault()
      self.popupDispose()


  popupDispose: ->
    @$container.removeClass('showed')
    self = @
    iframe = $('#iframe')
    @$container.one('transitionend webkitTransitionEnd', (e) =>
      e.stopPropagation()
      self.$popup.fadeOut(250)
      iframe.css('display', 'none')
    )

  showPlanPopup: (plan) ->
    @$popup.find("##{plan}").click()


  showTerms: ->
    $checkedFrield = $('.form_condition_agreement').find('input[type="checkbox"]')
    if not $checkedFrield.prop('checked')
      $checkedFrield.prop('checked', true)



  switchAction: ($item) ->
    switch $item.data('action')
      when 'plan' then @showPlanPopup($item.data('plan'))
      when 'terms' then @showTerms()
      else  throw new Error("Load action: #{$item.data('action')} doesn't exist")
    @popupDefaultAction($item)


  popupDefaultAction: ($item) ->
    @$popup.find("[data-popup='#{$item.data('action')}']").css('display' , 'block').siblings('.e-popup_section').css('display', 'none')
    @$container = $('.e-popup_container')
    self = @
    @$popup.fadeIn(300, ->
      self.$container.addClass('showed')
    )


Validation =
  validate: ->
    noError = true
    $(@).find('.validate').each ->
      type = $(@).data 'validate'
      Validation.validate.call(@)
      result = Validation.validation[type](@)
      if not result
        $(@).addClass 'input_error'
        $(@).next('.pers_info_input_error').slideDown(150)
        noError = false
        return
      else
        $(@).removeClass 'input_error'
        $(@).next('.pers_info_input_error').slideUp(150)
        return
    noError


  validation:
    text: (element) ->
      /([^\s*$])/.test(element.value)
    phone: (element) ->
      /\+?\d{2,}/.test(element.value)
    email: (element) ->
      /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(element.value)
    checkbox: (element) ->
      checked = $(element).is(':checked')
      checked


  submit: ->
    $activeForm = $(@)
    if not Validation.validate.call(@)
      false
    else
      Payment.sendRequest($activeForm)
    false

  clearFields: ($form) ->
    $form[0].reset()


Payment =
  sendRequest: ($activeForm) ->
    self = @
    $.ajax
      url: '/en/api/payment/'
      data: $activeForm.serialize()
      type: 'POST'
      success: (data) ->
        self.createIframe(data)
      error: ->
        console.log 'error'

  createIframe: (src)->
    @$iframe = $('#iframe')
    @$iframe.attr('src', src)
    @$iframe.load( =>
      @loadComplete()
    )

  loadComplete: ->
    @$iframe.fadeIn(400)