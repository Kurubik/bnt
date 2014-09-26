$(->
  Popups.init()
  FormAction.init()
)


Popups =
  init: ->
    $clickItem = $('.show_popup')
    self = @
    $closePopup = $('.close_popup')
    $closeConfirm = $('.close_confirm')
    $confirmButton = $('.confirm_popup')
    @$popup = $('#popup')
    @$loader = $('#loader')
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
    @$loader.css('display', 'none')
    @$container.removeClass('showed')
    self = @
    iframe = $('.iframe_holder')
    @$container.one('transitionend webkitTransitionEnd', (e) =>
      e.stopPropagation()
      self.$popup.fadeOut(250)
      iframe.css('display', 'none')
    )

  showPlanPopup: (plan) ->
    @$popup.find("##{plan}").click()

  showTerms: ->
    $checkedFrield = $('.form_condition_agreement').find('input[type="checkbox"]')
#    if not $checkedFrield.prop('checked')
#      $checkedFrield.prop('checked', true)


  switchAction: ($item) ->
    switch $item.data('action')
      when 'plan' then @showPlanPopup($item.data('plan'))
      when 'terms' then @showTerms()
      else false

    @popupDefaultAction($item.data('action'), false)

  popupDefaultAction: (action, loader) ->
    @$popup.find("[data-popup='#{action}']").css('display' , 'block').siblings('[data-popup]').css('display', 'none')
    @$container = $('.e-popup_container')
    self = @
    if not loader
      @$popup.fadeIn(300, ->
        self.$container.addClass('showed')
      )
    else
      @$container.addClass('showed')
      @$loader.css('display', 'none')
      @$popup.find('.e-popup_section').removeClass('hidden')


FormAction =
  init: ->
    $switcher = $('.form_switch')
    $paySwitch = $('.payment_value')
    self = @

    $switcher.on 'click', ->
      self.switchForm($(@).data('form'))

    $paySwitch.on 'click', ->
      $(@).closest('form').data('form_action', $(@).val())


  switchForm: (action) ->
    switch action
      when 'individual' then @showIndividual()
      when 'legal' then @showLegal()
      else false

  showIndividual: ->
    $('.form_input-company').stop().slideUp(250, ->
      $(@).find('input').removeClass('validate')
    )

  showLegal: ->
    $('.form_input-company').stop().slideDown(250, ->
      $(@).find('input').addClass('validate')
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
      Popups.$loader.css('display', 'block')
      if Popups.$popup.css('display') == 'block'
        $('.e-popup_section').addClass('hidden')
      else
        Popups.$popup.fadeIn(300)

      Payment.switchAction($activeForm)
    false

  clearFields: ($form) ->
    $form.find('input[type="text"]').each( ->
      $(@).val('')
    )



Payment =
  currentData : null
  switchAction: ($activeForm) ->
    switch $activeForm.data('form_action')
      when 'payment' then @sendRequest($activeForm)
      when 'bank' then @sendEmail($activeForm, 'bank')
      when 'business' then @sendEmail($activeForm, 'business')
      else false


  sendEmail: ($activeForm, action) ->
    $.ajax
      url: "/en/api/email/#{action}/"
      data: $activeForm.serialize()
      type: 'POST'
      success: (data) ->
        if data
          Popups.popupDefaultAction('sent', true)
          Validation.clearFields($activeForm)
        else
          Popups.popupDefaultAction('error', true)
      error: ->
        Popups.popupDefaultAction('error', true)


  sendRequest: ($activeForm) ->
    self = @
    formData = $activeForm.serialize()
    if @currentData isnt formData
      @currentData = formData
      $.ajax
        url: '/en/api/payment/'
        data: @currentData
        type: 'POST'
        success: (data) ->
          if data
            self.createIframe(data)
          else
            @currentData = null;
            Popups.popupDefaultAction('error', true)
        error: ->
          @currentData = null;
          Popups.popupDefaultAction('error', true)
    else
      @loadComplete()


  createIframe: (src) ->
    @$iframe = $('#iframe')
    @$iframe.attr('src', src)
    @$iframe.load( =>
      @loadComplete()
    )


  loadComplete: ->
    Popups.popupDefaultAction('iframe', true)