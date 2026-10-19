
function nouveau(lien, method = 'GET') { //alert(lien);
  document.getElementById('idenreg').value = null;
  document.forms["myform"].method = method;
  document.forms["myform"].action = lien;
  document.forms["myform"].submit();
}

function afficher(lien, method = 'GET') {
  const form = document.forms.namedItem('myform');
  const destination = '/' + lien.trimStart();

  if (form) {
    form.method = method;
    form.action = destination;
    form.submit();
    return;
  }

  window.location.assign(destination);
}

function valider(lien, id = null, method = 'POST') {
  let chp = '';
  let labels = '';
  let conf = document.getElementById('config').value; //alert(conf);
  switch (conf) {
    case 'categories':
      chp = 'libelle';
      labels = 'le libellé';
      break;

  }
  // alert(conf);

  if (controleVide(chp, labels)) {
    document.getElementById('idenreg').value = id;
    document.forms["myform"].method = method;
    document.forms["myform"].action = '/' + lien;
    document.forms["myform"].submit();
  }

}


function controleVide(champs, labels) {
  if (champs !== '' && labels !== '') {
    var tch = champs.split('|');
    var tlab = labels.split('|'); //alert(tch.length + ' ' + tlab.length);
    if (tch.length !== tlab.length) {
      alert("Les paramètres ne concordent pas");
    } else {
      var n = tch.length;
      for (let i = 0; i < n; i++) {
        if (!document.getElementById(tch[i])) alert(tch[i]);
        if (document.getElementById(tch[i]).value === '' | document.getElementById(tch[i]).value === '0') {
          alert("Vous devez renseigner " + tlab[i]);
          document.getElementById(tch[i]).focus();
          return false;
        }
      }
    }
  }

  return true;
}

function typenumerique(X) {
  result = "";

  for (I = X.value.length - 1; I >= 0; I--) {
    character = X.value.charAt(I);
    if (!/[0-9]/.test(character))
      continue;
    result = character + result;
    //            cpt++;
    //            if (cpt % 3 === 0 && I > 0)
    //                result = " " + result;
  }
  X.value = result;
}
function typemontant(X) {
  result = "";
  cpt = 0;
  for (I = X.value.length - 1; I >= 0; I--) {
    character = X.value.charAt(I);
    if (!/[0-9]/.test(character))
      continue;
    result = character + result;
    cpt++;
    if (cpt % 3 === 0 && I > 0)
      result = " " + result;
  }
  X.value = result;
}

function formatmontant(nb) {
  nb = String(nb);
  // alert(nb.length);
  result = "";
  cpt = 0;
  for (I = nb.length - 1; I >= 0; I--) {
    character = nb.charAt(I);
    if (!/[0-9]/.test(character))
      continue;
    result = character + result;
    cpt++;
    if (cpt % 3 === 0 && I > 0)
      result = " " + result;
  }
  return result;
}

function typetelephone(X) { //alert(X.name);


  result = "";
  cpt = 0;

  //       if(X.value.length-1 < 10){
  for (I = X.value.length - 1; I >= 0; I--) {
    character = X.value.charAt(I);
    if (!/[0-9]/.test(character))
      continue;
    result = character + result;
    cpt++;

    if (cpt % 2 === 0 && I > 0)
      result = " " + result;
  }
  //       }
  X.value = result;
}


function sansEspace(chaine) {
  return chaine.replace(/\s/g, "");
}


function enMajuscule(idchamp, nbCar = 0) {
  var m = document.getElementById(idchamp).value;
  //var t= parseInt(m.length);
  if (parseInt(nbCar) > 0) {
    if (parseInt(m.length) <= parseInt(nbCar))
      document.getElementById(idchamp).value = document.getElementById(idchamp).value.toUpperCase();
  } else {
    document.getElementById(idchamp).value = document.getElementById(idchamp).value.toUpperCase();
  }
}
function enMinuscule(idchamp, nbCar = 0) {
  var m = document.getElementById(idchamp).value;
  //var t= parseInt(m.length);
  if (parseInt(nbCar) > 0) {
    if (parseInt(m.length) <= parseInt(nbCar))
      document.getElementById(idchamp).value = document.getElementById(idchamp).value.toLowerCase();
  } else {
    document.getElementById(idchamp).value = document.getElementById(idchamp).value.toLowerCase();
  }
}


function appliqueDatatable() {
  $("#example1").DataTable({
    "responsive": true, "lengthChange": false, "autoWidth": false,
    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
  }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  $('#example2').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
  });
}

function appliqueClassDatatable() {
  $(".tableDT").DataTable({
    "responsive": true, "lengthChange": true, "autoWidth": true,
    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
  }).buttons().container().appendTo('.tableDT_wrapper .col-md-6:eq(0)');

}


function modifier(cas, idenreg) { //alert('M'+ cas + 'M' );
  document.getElementById('idenreg').value = idenreg;
  document.getElementById('config').value = cas;
  let lien = "/" + cas + "/" + idenreg;
  document.forms["myform"].method = 'GET';
  document.forms["myform"].action = lien;
  document.forms["myform"].submit();
}


function supprimer(cas, idenreg) {
  var rep = confirm("Voulez vous vraiment supprimer cet enregistrement?", okLabel = "oui");
  if (rep) {
    document.getElementById('idenreg').value = idenreg;
    document.getElementById('config').value = cas;
    let lien = "/" + cas + "/" + idenreg;
    document.forms["myform"].method = 'GET';
    document.forms["myform"].action = lien;
    document.forms["myform"].submit();
  }
}

function valCoche(idchp) {
  let v = document.getElementById(idchp).checked;
  document.getElementById(idchp).value = v === true ? 1 : 0;

}



function ajoutpanier(idprod, qte = 1) { //alert('Merci');
  //alert('Produit ajouté au panier ');
  $('#idproduitAjout').val(idprod);
  $('#qteAjout').val(qte);
  // alert(idprod + ' qte = ' + qte);
  // document.forms["myform"].method = method;
  document.forms["formAddCart"].action = '/add-cart';
  document.forms["formAddCart"].submit();
}

function retraitdupanier(idprod) {
  //alert('Produit ajouté au panier ');
  $('#idproduitAjout').val(idprod);

  document.forms["formAddCart"].action = '/minus-cart';
  document.forms["formAddCart"].submit();
}

function addcart(idprod, idchpqte) {
  var v = parseInt($('#' + idchpqte).val())
  ajoutpanier(idprod, v);
}

function plusqte(idchp) {
  var v = parseInt($('#' + idchp).val()) || 1;
  // alert('Merci ' + v);
  $('#' + idchp).val(v + 1);
}

function minusqte(idchp) {
  var v = parseInt($('#' + idchp).val()) || 1;
  $('#' + idchp).val(Math.max(1, v - 1));
}





appliqueClassDatatable();


$('.select2').select2();
