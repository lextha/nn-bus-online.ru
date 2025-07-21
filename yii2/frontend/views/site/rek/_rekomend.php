<div id="yandex_rtb_R-A-196619-32"></div>
<script>
    
window.yaContextCb.push(()=>{
  Ya.Context.AdvManager.renderFeed({
    renderTo: 'yandex_rtb_R-A-196619-32',
    blockId: 'R-A-196619-32'
  })
})
const feed = document.getElementById('yandex_rtb_R-A-196619-32'); 
  const callback = (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        Ya.Context.AdvManager.destroy({blockId: 'R-A-196619-25'});
      }
    });
  };
  const observer = new IntersectionObserver(callback, {
    threshold: 0,
  });
  observer.observe(feed);
</script>