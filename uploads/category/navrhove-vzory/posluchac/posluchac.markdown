Též **Pozorovanel** (Observer), nebo **Předplatitel** (Subscriber).

Řeší typickou úlohu, kdy objekt čeká na nějakou událost (např. stisk klávesy, echo od serveru).

Dvojí způsob řešeni:

1.  objekt neustále testuje, zda událost nenastala - nevhodné
2.  objekt se zaregistruje u zdroje událostí a nic nedělá; až událost nastane, zdroj mu pošle domluvenou zprávu - mnohem výhodnější

Zdroj zpráv se označuje jako **Vysílač** nebo **Pozorovaný** nebo **Vydatavel**

Tento návrhový vzor je tedy vždy dvojice **Vysílač - Posluchač** nebo (**Pozorovaný - Pozorovatel**, **Vydavatel - Předplatitel**)

Nejblíže k reálnému životu je _Vydavatel - Předplatitel_, protože _Předplatitelé_ se musejí u _Vydavatele_ zaregistrovat.

V Javě má tento návrhový vzor přímo předlohu - třída _Observable_ a rozhraní _Observer_.