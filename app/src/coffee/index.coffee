$(->
  $slideHover.init()
  console.log 'da'
)

$slideHover = {

  distMetric:(x,y,x2,y2) ->
    xDiff = x - x2;
    yDiff = y - y2;
    return (xDiff * xDiff) + (yDiff * yDiff)

  closestEdge: (x,y,w,h)->
    topEdgeDist = $slideHover.distMetric(x,y,w/2,0);
    bottomEdgeDist = $slideHover.distMetric(x,y,w/2,h);
    leftEdgeDist = $slideHover.distMetric(x,y,0,h/2);
    rightEdgeDist = $slideHover.distMetric(x,y,w,h/2);
    min = Math.min(topEdgeDist,bottomEdgeDist,leftEdgeDist,rightEdgeDist);
    console.log min
    switch min
      when leftEdgeDist
        console.log min
        return "left"
      when rightEdgeDist
        console.log min
        return "right"
      when topEdgeDist
        console.log min
        return "top"
      when bottomEdgeDist
        console.log min
        return "bottom"

  init: ->
    $('.e-quarterblock').hover((e)->
      edge = $slideHover.closestEdge(e.pageX, e.pageY, 300, 300)
    )
}