Přidání dodatečné funkcionality skupině tříd, aniž bychom museli tyto třídy měnit a dávat do nich stejný/podobný kód.

Instance _Služebníka_ obsluhují instance tříd požadujících novou funkčnost.

Musí existovat dohoda mezi _Služebníkem_ a obsluhovanými
- obsluhovaní musí ve svém rozhraní popsat, co mají umět
- _služebník_ má toto rozhraní jako typ parametru svých obsluhujících metod
- požadovaná funkčnost obsluhovaných se zajistí tím, že se posílá zpráva _Služebníkovi_ a reference na obsluhovanou instanci je skutečným parametrem této zprávy.

Při implementaci _Služebníka_ je výhodné, aby poskytoval co nejužší služby (nic konkrétního)
- tím bude moci v budoucnu obsloužit mnohem větší počet zájemců než při komplexních službách

Snažíme se, aby v rozhraní bylo jen nutné minimum metod.

Další metody společné obsluhovaným objektům dáváme do jiného či jiných rozhraní.

Třída může implementovat tolik rozhraní, kolik chce - musí ale implementovat všechny metody všech rozhraní
- to umožňuje vytvářet specializované služebníky.