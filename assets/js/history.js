/* Documentation links remain usable when native dialogs are unavailable. */
(function () {
 'use strict';
 var dialog=document.getElementById('pwfl-history'),opener;
 if(!dialog||dialog.dataset.pwflBound||!window.HTMLDialogElement)return;
 dialog.dataset.pwflBound='1';var nativeDialog=window.HTMLDialogElement.prototype;
 /** Close with the native method; the form remains a fallback without JavaScript. */
 function close(){if(dialog.open)nativeDialog.close.call(dialog);}
 document.addEventListener('click',function(event){
  var trigger=event.target.closest('[data-pwfl-history]');
  if(!trigger||event.button!==0||event.ctrlKey||event.metaKey||event.shiftKey||event.altKey)return;
  try{opener=trigger;dialog.hidden=false;nativeDialog.showModal.call(dialog);
   if(!dialog.open)throw new Error('Dialog did not open');
   dialog.querySelector('[data-pwfl-history-close]').focus();event.preventDefault();
  }catch(error){dialog.hidden=true;}
 },true);
 dialog.addEventListener('click',function(event){
  if(event.target.closest('[data-pwfl-history-close]')){close();return;}
  if(event.target===dialog){var box=dialog.getBoundingClientRect();if(event.clientX<box.left||event.clientX>box.right||event.clientY<box.top||event.clientY>box.bottom)close();}
 });
 dialog.addEventListener('cancel',function(){dialog.hidden=true;});
 dialog.addEventListener('close',function(){dialog.hidden=true;if(opener&&opener.isConnected)opener.focus();});
}());
