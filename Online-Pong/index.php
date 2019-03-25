<script>
checkOnline();

function checkOnline(){

    let xhr;
    if (window.XMLHttpRequest){
        xhr = new XMLHttpRequest();
    }
    else {
        xhr = new ActiveXObject("Microsoft.XMLHttp");
    }
    xhr.open("GET", "data-parser.php?checkOnline=true", true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200){
            console.log(this.responseText);
        }
    }

    xhr.send();
}
</script>