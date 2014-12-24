function unhideWindow()
{
   document.all.idx_loadinghide.style.display='';
}
      
function showLoading()
{
   if (document.body.clientHeight)
   {
      clientWidth = document.body.clientWidth;
      clientHeight = document.body.clientHeight;
   }
   else
   {
      clientWidth = window.innerWidth;
      clientHeight = window.innerHeight;
   }

   var centerWidth   = Math.round((clientWidth / 2)  - (151 / 2));
   var centerHeight  = Math.round((clientHeight / 2) - (38 / 2));

   document.write('<div id="idx_loadingscreen" style="position:absolute; margin: 0px; left: ' +centerWidth +'px; top:' +centerHeight +'px; height: 38px; width: 151px; z-index: 1000">');
   document.write('<table border="0" cellpadding="0" cellspacing="0" style="border:3px solid #CCCCCC">');
   document.write('<tr>');
   document.write('<td><img src="./images/page/loading.gif"></td>');
   document.write('</tr>');
   document.write('</table>');
   document.write('</div>');
}

function hideLoading()
{
   document.all.idx_loadingscreen.style.display = 'none';
}