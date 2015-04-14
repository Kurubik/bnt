$(->
  $slideHover.init()
)

$slideHover = {

  distMetric: (x,y,x2,y2) ->
    xDiff = x - x2;
    yDiff = y - y2;
    return (xDiff * xDiff) + (yDiff * yDiff)

  closestEdge: (x,y,w,h,$this)->
    topEdgeDist = $slideHover.distMetric(x,y,w/2,0);
    bottomEdgeDist = $slideHover.distMetric(x,y,w/2,h);
    leftEdgeDist = $slideHover.distMetric(x,y,0,h/2);
    rightEdgeDist = $slideHover.distMetric(x,y,w,h/2);
    min = Math.min(topEdgeDist,bottomEdgeDist,leftEdgeDist,rightEdgeDist)

    switch min
      when leftEdgeDist
        block = $this.find('[data-hover="left"]')
        block.addClass('e-quarterblock_hover_active')
      when rightEdgeDist
        block = $this.find('[data-hover="right"]')
        block.addClass('e-quarterblock_hover_active')
      when topEdgeDist
        block = $this.find('[data-hover="top"]')
        block.addClass('e-quarterblock_hover_active')
      when bottomEdgeDist
        block = $this.find('[data-hover="bottom"]')
        block.addClass('e-quarterblock_hover_active')

  init: ->

    $('.e-quarterblock').on 'mouseenter',(e)->
      el_pos = $(@).offset();
      $slideHover.closestEdge(e.pageX - el_pos.left, e.pageY - el_pos.top, $(@).outerWidth(), $(@).outerHeight(), $(@))

    $('.e-quarterblock').on 'mouseleave', ->
      $(@).find('.e-quarterblock_hover').removeClass('e-quarterblock_hover_active')

}