<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Document sans titre</title>
</head>

<body>

	<h1>Formatage par balises</h1>
	<h2>Description</h2>
	<p>
		Cette extension permet d'afficher plusieurs paragraphes dans un cadre. L'utilisation de balises définies selon les besoins permet de changer la police, le style (gras, italique, souligné), la taille, et la couleur des caractères.</p>
	<p>A cette fin, 2 méthodes sont disponibles :</p>
	<p>- La première pour définir une feuille de style :</p>
	<p><code>SetStyle(<b>string</b> tag, <b>string</b> family, <b>string</b> style, <b>int</b> size, <b>string</b> color [, <b>int</b> indent])</code>
	</p>

	<code><u>tag</u></code> : nom de la balise<br>
	<code><u>family</u></code> : famille de la police<br>
	<code><u>style</u></code> : N (normal) ou combinaison de B, I, U<br>
	<code><u>size</u></code> : taille<br>
	<code><u>color</u></code> : couleur (composantes RVB séparées par une virgule)<br>
	<code><u>indent</u></code> : à spécifier pour la balise paragraphe ; indente la première ligne de la valeur indiquée
	<p> Il est possible d'utiliser des chaînes vides ou des valeurs nulles, sauf pour la balise paragraphe.<br> Les valeurs sont alors obtenues par héritage ; par exemple, avec &lt;p&gt;&lt;u&gt;, les valeurs non renseignées de &lt;u&gt; sont remplacées par celles de &lt;p&gt;.</p>

	<p> - La seconde pour afficher un contenu :<p>

	<p><code>WriteTag(<b>float</b> w, <b>float</b> h, <b>string</b> txt [, <b>int</b> border [, <b>string</b> align [, <b>int</b> fill [, <b>mixed</b> padding]]]])</code></p>
	<code><u>w</u></code> : largeur maximale de la ligne (0 pour aller d'une marge à l'autre)<br>
	<code><u>h</u></code> : hauteur d'une ligne<br>
	<code><u>txt</u></code> : texte à afficher - doit comporter au minimum une balise au début et à la fin pour définir un paragraphe<br>
	<code><u>border</u></code> : 0/1 - absence/existence (par défaut : 0)<br>
	<code><u>align</u></code> : justification du texte : L, R, C ou J (par défaut : J)<br>
	<code><u>fill</u></code> : 0/1 - absence/existence (par défaut : 0)<br>
	<code><u>padding</u></code> : soit une valeur numérique, soit une chaîne de "gauche,haut,bas,droit" avec 2, 3 ou 4 valeurs renseignées (par défaut : 0)
</body>
</html>