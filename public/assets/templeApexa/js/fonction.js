
function urlApplication(lien) {
  const base = document.querySelector('base');
  const baseUrl = base ? base.href : window.location.origin + '/';

  return new URL(String(lien).replace(/^\/+/, ''), baseUrl).href;
}

function nouveau(lien, method = 'GET') { //alert(lien);
  document.getElementById('idenreg').value = null;
  document.forms["myform"].method = method;
  document.forms["myform"].action = urlApplication(lien);
  document.forms["myform"].submit();
}

function afficher(lien, method = 'GET') {
  const form = document.forms.namedItem('myform');
  const destination = urlApplication(lien.trimStart());

  if (form) {
    form.method = method;
    form.action = destination;
    form.submit();
    return;
  }

  window.location.assign(destination);
}

function valider(lien, id = null, method = 'POST') {
  // alert(lien);
  let chp = '';
  let labels = '';
  let conf = document.getElementById('config').value; //alert(conf);
  switch (conf) {
   
    case 'utilisateur':
      chp = 'name|email|idrole';
      labels = "le nom|l'email|le profil";
      break;
    case 'transfert':
      chp = 'code|datetransfert|idcaisse|idmoyentransfert|idexpediteur|idrecepteur|iddepart|iddestination|taux|montdepot|montretrait|description';
      labels = "le code|la date du transfert|la caisse|le moyen de transfert|l'expéditeur|le récepteur|la ville de départ|la ville de destination|le taux de change|le montant déposé|le montant à retirer|la description";
      break;
    case 'expedition':
      chp = 'code|datenreg|idexpediteur|idrecepteur|idmoyentransport|iddepart|iddestination|marque';
      labels = "le code|la date d'enregistrement|l'expéditeur|le récepteur|le moyen de transport|la ville de départ|la ville de destination|la marque";
      break;
    case 'article':
      chp = 'idcategorie|libelle';
      labels = "la catégorie|la désignation de l'article";
      break;

  }
  // alert(conf);

  if (controleVide(chp, labels)) {
    if(conf === 'operation'){
      if(verifEquilibre() != false){
         document.getElementById('idenreg').value = id;
      document.forms["myform"].method = method;
      document.forms["myform"].action = urlApplication(lien);
      document.forms["myform"].submit();
      }
    }else{
      document.getElementById('idenreg').value = id;
      document.forms["myform"].method = method;
      document.forms["myform"].action = urlApplication(lien);
      document.forms["myform"].submit();
    }
   
    
  }

}


function controleVide(champs, labels) { //alert(champs + ' ' + labels); 
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

function valideretraitransfert(id){
  let datepayement  = document.getElementById('datepayement').value;
  let datetrait     = document.getElementById('datetransfert') ? document.getElementById('datetransfert').value : '';
  let montantPaye   = document.getElementById('montpaye').value;
  let observation   = document.getElementById('observation').value;
  let carteRecepteur = document.getElementById('carte_recepteur');
  let idtransfert   = document.getElementById('idtransfert').value;
  let btnValider    = document.querySelector("button[onclick='valideretraitransfert(" + id + ")']");

  // --- Validations ---
  if (datepayement === "") {
    alert("La date de paiement est obligatoire");
    return false;
  }

  if (datetrait !== "" && datepayement < datetrait) {
    alert("La date de paiement ne peut pas être antérieure à la date de transfert");
    return false;
  }
  if (montantPaye === "" || !Number.isFinite(Number(montantPaye)) || Number(montantPaye) < 0) {
    alert("Le montant payé doit être un nombre positif ou nul");
    return false;
  }

  // --- Envoi AJAX ---
  const csrfToken = document.querySelector('meta[name="csrf-token"]')
                    ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    : '';

  if (btnValider) {
    btnValider.disabled = true;
    btnValider.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enregistrement...';
  }
  const receiptWindow = window.open('', '_blank');

  const formData = new FormData();
  formData.append('idtransfert',  idtransfert);
  formData.append('datepayement', datepayement);
  formData.append('montpaye', montantPaye);
  formData.append('observation',  observation);
  if (carteRecepteur && carteRecepteur.files.length > 0) {
    formData.append('carte_recepteur', carteRecepteur.files[0]);
  }
  formData.append('_token',       csrfToken);

  fetch(urlApplication('enregistrer-retraitransfert'), {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      if (receiptWindow) {
        receiptWindow.location.href = data.receipt_url;
      } else {
        window.open(data.receipt_url, '_blank');
      }
      // Fermer le modal et recharger la liste
      var modalEl = document.getElementById('modalRtrait');
      var modal   = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.hide();
      // Afficher un toast de succès si disponible, sinon alert
      if (typeof afficherToast === 'function') {
        afficherToast('success', data.message);
      } else {
        alert(data.message);
      }
      // Recharger la page pour mettre à jour l'état dans la liste
      setTimeout(() => location.reload(), 800);
    } else {
      if (receiptWindow) receiptWindow.close();
      alert("Erreur : " + data.message);
      if (btnValider) {
        btnValider.disabled = false;
        btnValider.innerHTML = 'Valider le retrait';
      }
    }
  })
  .catch(error => {
    if (receiptWindow) receiptWindow.close();
    console.error('Erreur réseau :', error);
    alert("Une erreur réseau est survenue. Veuillez réessayer.");
    if (btnValider) {
      btnValider.disabled = false;
      btnValider.innerHTML = 'Valider le retrait';
    }
  });
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
  var preloader = document.getElementById('preloader');
  if (preloader) preloader.style.display = 'block';
  document.getElementById('idenreg').value = idenreg;
  document.getElementById('config').value = cas;
  let lien = cas + "/" + idenreg;
  document.forms["myform"].method = 'GET';
  document.forms["myform"].action = urlApplication(lien);
  document.forms["myform"].submit();
}


function supprimer(cas, idenreg) {
  var rep = confirm("Voulez vous vraiment supprimer cet enregistrement?", okLabel = "oui");
  if (rep) {
    var preloader = document.getElementById('preloader');
    if (preloader) preloader.style.display = 'block';
    document.getElementById('idenreg').value = idenreg;
    document.getElementById('config').value = cas;
    let lien = cas + "/" + idenreg;
    document.forms["myform"].method = 'GET';
    document.forms["myform"].action = urlApplication(lien);
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
  document.forms["formAddCart"].action = urlApplication('add-cart');
  document.forms["formAddCart"].submit();
}

function retraitdupanier(idprod) {
  //alert('Produit ajouté au panier ');
  $('#idproduitAjout').val(idprod);

  document.forms["formAddCart"].action = urlApplication('minus-cart');
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

function onChangeGet(conf,idcombo = '') {     //alert($('#'+idcombo).val()) ; 
        
        // document.forms["myform"].idcombo = $('#'+idcombo).val();
//  const newInput = document.createElement('input');
//         newInput.type = 'hidden';
//         newInput.name = idcombo;
//         newInput.id = idcombo;
//         newInput.value = $('#'+idcombo).val();
//         document.forms["myform"].append(newInput);

        document.forms["myform"].action = urlApplication(conf);
        document.forms["myform"].method = 'GET';
        document.forms["myform"].submit();
    }

function reinitialiser(iduser) {
    document.getElementById('reset_iduser').value = iduser;
    var modal = new bootstrap.Modal(document.getElementById('modalResetPassword'));
    modal.show();
}

$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});


appliqueClassDatatable();
// appliqueDatatable();

// Initialize Select2 for normal selects
$('.select2').select2();

// Initialize Select2 inside modals when they are shown to ensure proper dropdownParent
$('#modalDebit').on('shown.bs.modal', function () {
    $('#modalDebit .select2').select2({ dropdownParent: $('#modalDebit') });
});
$('#modalCredit').on('shown.bs.modal', function () {
    $('#modalCredit .select2').select2({ dropdownParent: $('#modalCredit') });
});


function ouvrirModalRetrait(id) {
    var loading = document.getElementById("loadingInfos");
    var content = document.getElementById("contentInfos");

    // Réinitialiser le contenu et afficher le spinner
    loading.style.display = "block";
    content.innerHTML = "";

    // Ouvrir le modal Bootstrap
    var modalEl = document.getElementById("modalRtrait");
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();

    fetch(urlApplication("api/retraitransfert/" + encodeURIComponent(id)))
        .then((response) => {
            if (!response.ok) throw new Error("HTTP " + response.status);
            return response.text();
        })
        .then((html) => {
            loading.style.display = "none";
            content.innerHTML = html;
        })
        .catch((error) => {
            loading.style.display = "none";
            content.innerHTML =
                '<div class="alert alert-danger">Une erreur est survenue lors du chargement des données.</div>';
            console.error("Erreur:", error);
        });
}

function rechercheTarifExpoedition(grille, cle) {
  let trouv = false;
  let i = 0;
  let tarif = null;
  for (let i = 0; i < grille.length; i++) {
    const element = grille[i];
    if(element[cle]){
      let tcle = element[cle].split('|');
      let prix = tcle[0];
      tarif = parseFloat(prix);
      
    //   let maxpois = parseFloat(tcle[1]);
    //  if (poids <= maxpois ) tarif = prix;

    }
    
  }
  
  return tarif;
  
}
