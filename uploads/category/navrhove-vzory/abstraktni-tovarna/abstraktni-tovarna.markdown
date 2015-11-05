Defunuje rozhraní pro tvorbu celé rodiny soubisejících nebo závislých objektů a tím odděluje klienta od vlastního procesu vytváření objektů.

Abstraktní továrna umožňuje jednoduše ovlivnit chování celého systému.

Existuje X výrobců a každý z nich vyrábí Y různých (na sobě nezávislých) výrobků
- výrobek Y1 od firmy A má **stejnou funkčnost** jako výrobek Y1 od firmy B; může mít jiný vzhled, odlišný způsob práce, apod.

Cílem je, aby uživatel (aplikace) používala všechny výrobky právě jednoho výrobce
- a bylo možné jednoduše (jedním příkazem) stanovit, který výrobce to bude
- po výběru výrobce jsou všechny výrobky vytvořeny automaticky