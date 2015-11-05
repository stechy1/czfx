V informatice je strom široce využívanou datovou strukturou, která představuje stromovou strukturu s propojenými uzly.

V teorii grafů se jako strom označuje neorientovaný graf, který je souvislý a neobsahuje žádné kružnice.

### Základní pojmy

*   _vrchol_ - prvek ADT strom
*   _hrana_ - spojení dvou vrcholů

### Definice

Mějme jeden vrchol. Nechť je tento vrchol stromem, potom se bude nazývat kořenem stromu.

Nechť x je vrchol a T1, T2, ..., Tn jsou stromy. Strom je vrchol x spojený s kořeny stromů T1, T2, ..., TN a x je kořenem.

T1, T2, ..., Tn - jsou podstromy,

kořeny T1, T2, ..., Tn - jsou následníci (synové) vrcholu x

vrchol x - je předchůdce (otec) kořenů T1, T2, ..., Tn

_list_ - vrchol bez následníků

_vnitřní vrchol_ - vrchol, který není listem

_cesta_ - posloupnost vrcholů, ve které po sobě jdoucí vrcholy jsou spojeny hranou

_délka cesty_ - počet hran cesty

**Poznámka:** Ke každému vrcholu je z kořene právě jedna cesta.

_hloubka_ vrcholu ve stromě (úroveň, na které se nachází) - délka cesty z kořene k vrcholu

**Poznámka:** Úroveň (hloubka) kořene stromu je nulová

_výška stromu_ - maximální hloubka stromu

**Binární strom** je prázdný strom anebo vrchol, který má levý a pravý podstrom, které jsou binární stromy.

Strom můžeme implementovat polem, nebo spojovou strukturou.

### Implementace polem

**Pozice vrcholů** úplného binárního stromu lze očíslovat:

kořenu přiřadíme 1

levému následníku 2

pravému následníku 3

pokračujeme na každé úrovni zleva až do konce

_pozice_ = index prvkku pole

je-li na ní vrchol - klíč, není-li - např. -1

Má-li vrchol hodnotu indexu i, potom levý následník má index 2i, pravý následník má index 2i + 1 a předchůdce, pokud existuje má index i/2 (celočíselně)

Musíme vytvořit pole pro předpokládanou mximální velikost stromu. Navíc musí obsahovat i prvky pro pozice neobsazené vrcholy.

**Poznámka:** Uvedené vztahy platí, má-li kořen stromu index 1\. Pokud bychom kořen umístili do prvku pole s indexem 0, tyto vztahy by bylo nutno upravyt.

### Implementace spojovou strukturou

```
class Vrchol {
    int klic;
    Vrchol levy;
    Vrchol pravy;
    void tiskVrcholu() {
        System.out.print(klic + " ");
    }
}
```

### Průchody stromem

*   _Přímý průchod (preorder)_ - navštívíme vrchol, potom levý a pravý podstrom
*   _Vnitřní průchod (inorder) - navštívíme levý podstrom, vrchol a pravý podstrom_
*   _Zpětný průchod (postorder)_ - navštívíme levý a pravý podstrom a potom vrchol

#### Rekurzivně

Rekurzivní průchod stromem pomocí _preorder_:

```
void pruchodR(Vrchol v) { 
    if (v == null)
        return;
    v.tiskVrcholu();
    pruchodR(v.levy);
    pruchodR(v.pravy);
}
Vrchol koren;
pruchodR (koren);

```

Průchod pomocí _inorder_ - posunutí řádku s tiskem mezi rekurzivní volání

Průchod pomocí _postorder_ - posunutí řádku s tiskem za obě rekurzivní volání

#### Nerekurzivně

```
void pruchod(Vrchol v) {
    VZasobnik z = new VZasobnik();
    z.push(v);
    while (!z.jePrazdny()) {
        v = z.pop();
        v.tiskVrcholu();
        if (v.pravy != null) z.push(v.pravy);
        if (v.levy != null) z.push(v.levy);
    }
}
```

#### Po úrovních

```
void pruchod(Vrchol v) {
    VFronta f = new VFronta();
    f.vloz(v);
    while (!f.jePrazdna()) {
        v = f.vyber ();
        v.tiskVrcholu();
        if (v.levy != null) f.vloz(v.levy);
        if (v.pravy != null) f.vloz(v.pravy);
    }
 }
```