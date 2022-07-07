/*
waitMe - 1.19 [31.10.17]
Author: Stampel emese
Github: 
*/


function lastSyncModfy(text){

  var now = new Date();
  jQuery('.last-sync').html('Utolsó szinkron: '+text+' | Kezdete: '+ now.toString()+' | Vége: ismeretlen | Össz futási idő: ismeretlen');

}

function endSyncLastSync(){

  window.top.location.reload(true);

}