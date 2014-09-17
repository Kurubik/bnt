$(->
  Plans.init()
)


Plans =
  init: ->
    $clickItem = $('[data-plan]')
    self = @
    $closePopup = $('.close_popup')
    @$popup = $('#popup')

    $clickItem.on 'click', (e) ->
      e.preventDefault()
      self.switchAction($(@))

    $closePopup.on 'click', (e) ->
      e.preventDefault()
      self.popupDispose()

    @$popup.on 'click', (e) ->
      if self.$popup.is(e.target)
        self.popupDispose()


  popupDispose: ->
    @$container.removeClass('showed')
    self = @
    @$container.one('transitionend webkitTransitionEnd', (e) =>
      e.stopPropagation()
      self.$popup.fadeOut(250)
    )

  showPlanPopup: (plan) ->
    @$popup.find("##{plan}").click()


  showTerms: ->


  switchAction: ($item) ->
    switch $item.data('action')
      when 'plan' then @showPlanPopup($item.data('plan'))
      when 'terms' then @showTerms()
      else  throw new Error("Load action: #{$item.data('action')} doesn't exist")
    @popupDefaultAction($item)


  popupDefaultAction: ($item) ->
    @$popup.find("[data-popup='#{$item.data('action')}']").css('display' , 'block')
    @$container = $('.e-popup_container')
    self = @
    @$popup.fadeIn(300, ->
      self.$container.addClass('showed')
    )

