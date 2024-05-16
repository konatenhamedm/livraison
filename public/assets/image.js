$(function(){
    
    const $containerImage = $('.proto-container-image');

var indexImage = $containerImage.find('.row-colonne-image').length;

const $addLinkImage = $('.add_ligne');


$addLinkImage.click(function(e) {

const $this  = $(this);
const proto_class = $this.attr('data-protoclass');
const name = $this.attr('data-protoname');
const $container = $($this.attr('data-container'));
// alert('')
// const name = $this.attr('data-protoname');
//const $container = $($this.attr('data-container'));


addLineImage($container, name, proto_class);



e.preventDefault(); // évite qu'un # apparaisse dans l'URL

KTImageInput.createInstances();
var imageInputElement = document.querySelector("#kt_image_input_control");
var imageInput = KTImageInput.getInstance(imageInputElement);

});

if (indexImage > 0) {
   
$('.row-colonne-image').each(function() {
    /* alert('') */
   const $this = $(this);
   addDeleteLinkImage($this);
   const $select = $this.find("select");
   //$deleteLink = $('<a href="#" class="btn btn-danger btn-xs" style="width: 114px;"><span class="fa fa-trash" ></span></a>');
   //$(".del-col-image").append($deleteLink)



   /*$select.each(function() {
       const $this = $(this);
       init_select2($this, null, '#exampleModalSizeSm2');
       if ($this.hasClass('select-type')) {
           let field_str = $this.find('option:selected').attr('data-require-fields');
           const $parent = $this.closest('.row-colonne-image');
           let fields = [];
           if (typeof field_str != 'undefined') {
               fields = field_str.split(',');
               for (let field of fields ) {
                   $parent.find('.' + field).removeClass('d-none');
               }
           } else {
               $parent.find('.source,.valeurs').addClass('d-none');
           }
       }
   });*/

});

}




// La fonction qui ajoute un formulaire Categorie
function addLineImage($containerImage, name, proto_class) {
// Dans le contenu de l'attribut « data-prototype », on remplace :
// - le texte "__name__label__" qu'il contient par le label du champ
// - le texte "__name__" qu'il contient par le numéro du champ

var $prototype = $($(proto_class).attr('data-prototype')
   .replace(new RegExp(name + 'label__', 'g'), 'Colonne ' + (indexImage+1))
   .replace(new RegExp(name, 'g'), indexImage));


init_select2($prototype.find('select'), null, '#exampleModalSizeSm2');


// On ajoute au prototype un lien pour pouvoir supprimer la prestation
// On ajoute le prototype modifié à la fin de la balise <div>
if(indexImage < 7) {    
            addDeleteLinkImage($prototype, name);
        $containerImage.append($prototype);
        }
indexImage++;
}


function addDeleteLinkImage($prototype, name = null) {
// Création du lien
$deleteLink = $('<a href="#" class="btn btn-danger btn-xs" style="width: 114px;"><span class="fa fa-trash" ></span></a>');
// Ajout du lien
$prototype.find(".del-col-image").append($deleteLink);



// Ajout du listener sur le clic du lien
$deleteLink.click(function(e) {
   const $this = $(this);
   const $parent = $(this).closest('.row-colonne-image');
   //console.log($(this).attr('data-parent'), $(this));
   $parent.remove();

   if (indexImage > 0) {
       indexImage -= 1;
   }

   e.preventDefault(); // évite qu'un # apparaisse dans l'URL
});
}
})