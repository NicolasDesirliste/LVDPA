<!-- 
 app/views/auth/register.php 

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - LVDPA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/LVDPA/public/CSS/diapocss/register.css">
</head>

// Commentage pour faire fonctionner le spa 
tout en gardant le formulaire intacte.
<body>
-->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/LVDPA/public/CSS/diapocss/register.css">

    <div data-page-title="Bienvenue sur la page d'inscription!">
    <div class="container">
        <div class="form-container">
            <div class="info-text">
                <p>Bienvenue dans le formulaire d'inscription - Merci de remplir tous les champs nécessaires</p>
            </div>
            
            <form action="/LVDPA/register" method="POST">
                <div class="input-container">
                    <i class="fa fa-briefcase icon"></i>
                    <select class="input-field" id="type_utilisateur" name="type_utilisateur" required>
                        <option value="">Vous êtes?</option>
                        <option value="particulier">Particulier</option>
                        <option value="avocat">Avocat</option>
                        <option value="psychologue">Psychologue</option>
                        <option value="mediateur">Médiateur indépendant</option>
                    </select>
                </div>

                <div class="input-container">
                    <i class="fa fa-map-marker icon"></i>
                    <select class="input-field" id="departement" name="departement" required>
                        <option value="">Indiquez votre département de résidence</option>
                        <option value="1">01 - Ain</option>
                        <option value="2">02 - Aisne</option>
                        <option value="3">03 - Allier</option>
                        <option value="4">04 - Alpes-de-Haute-Provence</option>
                        <option value="5">05 - Hautes-Alpes</option>
                        <option value="6">06 - Alpes-Maritimes</option>
                        <option value="7">07 - Ardèche</option>
                        <option value="8">08 - Ardennes</option>
                        <option value="9">09 - Ariège</option>
                        <option value="10">10 - Aube</option>
                        <option value="11">11 - Aude</option>
                        <option value="12">12 - Aveyron</option>
                        <option value="13">13 - Bouches-du-Rhône</option>
                        <option value="14">14 - Calvados</option>
                        <option value="15">15 - Cantal</option>
                        <option value="16">16 - Charente</option>
                        <option value="17">17 - Charente-Maritime</option>
                        <option value="18">18 - Cher</option>
                        <option value="19">19 - Corrèze</option>
                        <option value="20">20 - Corse</option>
                        <option value="21">21 - Côte-d'Or</option>
                        <option value="22">22 - Côtes-d'Armor</option>
                        <option value="23">23 - Creuse</option>
                        <option value="24">24 - Dordogne</option>
                        <option value="25">25 - Doubs</option>
                        <option value="26">26 - Drôme</option>
                        <option value="27">27 - Eure</option>
                        <option value="28">28 - Eure-et-Loir</option>
                        <option value="29">29 - Finistère</option>
                        <option value="30">30 - Gard</option>
                        <option value="31">31 - Haute-Garonne</option>
                        <option value="32">32 - Gers</option>
                        <option value="33">33 - Gironde</option>
                        <option value="34">34 - Hérault</option>
                        <option value="35">35 - Ille-et-Vilaine</option>
                        <option value="36">36 - Indre</option>
                        <option value="37">37 - Indre-et-Loire</option>
                        <option value="38">38 - Isère</option>
                        <option value="39">39 - Jura</option>
                        <option value="40">40 - Landes</option>
                        <option value="41">41 - Loir-et-Cher</option>
                        <option value="42">42 - Loire</option>
                        <option value="43">43 - Haute-Loire</option>
                        <option value="44">44 - Loire-Atlantique</option>
                        <option value="45">45 - Loiret</option>
                        <option value="46">46 - Lot</option>
                        <option value="47">47 - Lot-et-Garonne</option>
                        <option value="48">48 - Lozère</option>
                        <option value="49">49 - Maine-et-Loire</option>
                        <option value="50">50 - Manche</option>
                        <option value="51">51 - Marne</option>
                        <option value="52">52 - Haute-Marne</option>
                        <option value="53">53 - Mayenne</option>
                        <option value="54">54 - Meurthe-et-Moselle</option>
                        <option value="55">55 - Meuse</option>
                        <option value="56">56 - Morbihan</option>
                        <option value="57">57 - Moselle</option>
                        <option value="58">58 - Nièvre</option>
                        <option value="59">59 - Nord</option>
                        <option value="60">60 - Oise</option>
                        <option value="61">61 - Orne</option>
                        <option value="62">62 - Pas-de-Calais</option>
                        <option value="63">63 - Puy-de-Dôme</option>
                        <option value="64">64 - Pyrénées-Atlantiques</option>
                        <option value="65">65 - Hautes-Pyrénées</option>
                        <option value="66">66 - Pyrénées-Orientales</option>
                        <option value="67">67 - Bas-Rhin</option>
                        <option value="68">68 - Haut-Rhin</option>
                        <option value="69">69 - Rhône</option>
                        <option value="70">70 - Haute-Saône</option>
                        <option value="71">71 - Saône-et-Loire</option>
                        <option value="72">72 - Sarthe</option>
                        <option value="73">73 - Savoie</option>
                        <option value="74">74 - Haute-Savoie</option>
                        <option value="75">75 - Paris</option>
                        <option value="76">76 - Seine-Maritime</option>
                        <option value="77">77 - Seine-et-Marne</option>
                        <option value="78">78 - Yvelines</option>
                        <option value="79">79 - Deux-Sèvres</option>
                        <option value="80">80 - Somme</option>
                        <option value="81">81 - Tarn</option>
                        <option value="82">82 - Tarn-et-Garonne</option>
                        <option value="83">83 - Var</option>
                        <option value="84">84 - Vaucluse</option>
                        <option value="85">85 - Vendée</option>
                        <option value="86">86 - Vienne</option>
                        <option value="87">87 - Haute-Vienne</option>
                        <option value="88">88 - Vosges</option>
                        <option value="89">89 - Yonne</option>
                        <option value="90">90 - Territoire de Belfort</option>
                        <option value="91">91 - Essonne</option>
                        <option value="92">92 - Hauts-de-Seine</option>
                        <option value="93">93 - Seine-Saint-Denis</option>
                        <option value="94">94 - Val-de-Marne</option>
                        <option value="95">95 - Val-d'Oise</option>
                        <!-- DOM-TOM simplifié -->
                        <option value="971">971 - Guadeloupe</option>
                        <option value="972">972 - Martinique</option>
                        <option value="973">973 - Guyane</option>
                        <option value="974">974 - La Réunion</option>
                        <option value="976">976 - Mayotte</option>
                    </select>
                </div>

                <div class="input-container">
                    <i class="fa fa-user icon"></i>
                    <input class="input-field" 
                           type="text" 
                           placeholder="Entrez votre pseudonyme" 
                           name="pseudo"
                           id="pseudo"
                           required>
                </div>
                
                <div class="input-container">
                    <i class="fa fa-envelope icon"></i>
                    <input class="input-field" 
                           type="email" 
                           placeholder="Quelle est votre adresse email?" 
                           name="email" 
                           id="email"
                           required>
                </div>
                
                <div class="input-container">
                    <i class="fa fa-key icon"></i>
                    <input class="input-field" 
                           type="password" 
                           placeholder="Choisissez votre mot de passe" 
                           name="mot_de_passe" 
                           id="mot_de_passe"
                           required>
                </div>

                <div class="input-container">
                    <i class="fa fa-key icon"></i>
                    <input class="input-field" 
                           type="password" 
                           placeholder="Confirmez votre mot de passe" 
                           name="mot_de_passe_confirmation" 
                           id="mot_de_passe_confirmation"
                           required>
                </div>

                <button type="submit" class="btn">Valider mes informations & m'inscrire</button>
            </form>
            
            <footer>
                <h2>Bienvenue dans l'équipe!</h2>
            </footer>
        </div>
    </div>
</div> <!-- div fermante pour le SPA --> 
<!-- </body>
</html> -->