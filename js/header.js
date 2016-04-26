<script>

function connexion() {
    var adresse = prompt("Adresse", "");
    var pseudo	 = prompt("Pseudo", "");
    
    if (person != null) {
        document.getElementById("demo").innerHTML =
        "Hello " + person + "! How are you today?";
    }
}
