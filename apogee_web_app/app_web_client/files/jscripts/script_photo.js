
function animationPhoto(img, h, maxH) {
    if (h === 0) {
        img.style.display = 'inline-block';
    }
    img.style.height = h + '%';
    if (h < maxH) {
        setTimeout(function() {
            animationPhoto(img, h + 1, maxH);
        }, 30);
    } else {
        img.style.height = maxH + '%';
    }
}
/**
 * 
 * @returns {undefined} 
 * */
function telechargerPhoto(idInputFile,idImage) {
    var reader = new FileReader(),
            typesImg = ['png', 'jpg', 'jpeg'],
            fileInput = document.getElementById(idInputFile),
            img = document.getElementById(idImage);
    ;
    fileInput.onchange = function() {
        var file = this.files[0], imgType;
        imgType = file.name.split('.');
        imgType = imgType[imgType.length - 1];
        if (typesImg.indexOf(imgType.toLowerCase()) !== -1) {
            reader.onload = function() {
                img.src = this.result;
            };
            reader.readAsDataURL(file);
            img.style.display = 'none';
            animationPhoto(img, 0, 30);
        }
        else {
            alert('teléchargez une photo de type png, jpg ou jpeg');
            img.style.display = 'none';
            //il faut de eviter ce fichier qui est pas autorisé.même alert laisse le ficher selectionné.
        }
    };
}
