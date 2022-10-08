<script type="text/javascript" src="{{asset('assets/js/croppie.min.js')}}"></script>
<script type="text/javascript">
if(image == undefined) {
    image = null;
}
$image_crop = $('#upload-image').croppie({
    enableExif: true,
    url: image,
    viewport: {width: 200,height: 200,type: 'square'},
    boundary: {width: 300,height: 300}
});
$('#images').on('change', function () { 
    var reader = new FileReader();
    reader.onload = function (e) {
        $image_crop.croppie('bind', {
            url: e.target.result
        }).then(function() {
            console.log('jQuery bind complete');
        });
    }
    reader.readAsDataURL(this.files[0]);
});
function addproducts() {
  if($('#images').length == 0) {
    //no image croppie
    $("#product_add_form").submit();
    return false;
  } 
    $image_crop.croppie('result', {
      type: 'canvas',
      size: 'viewport'
    }).then(function (response) {
      $("#imageBinary").val(response);
      $("#product_add_form").submit();
    });
  // }
}
function updateProducts() {
  // console.log(image);
  // if(image == null || image == '') {
  //   $("#pupdate").submit();
  // } else {
    //no image croppie
    if($('#images').length == 0) {
    //no image croppie
    $("#pupdate").submit();
    return false;
  } 
  
    $image_crop.croppie('result', {
      type: 'canvas',
      size: 'viewport'
    }).then(function (response) {
      console.log('response', response);
      $("#imageBinary").val(response);
      $("#pupdate").submit();
    });
  // }
}
</script>