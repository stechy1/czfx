Používáme, pokud chceme, aby metoda vracela více hodnot najednou.

K vytvoření přepravky nám stačí jednoduchá třída, která má tolik atributů, kolik potřebujeme předat hodnot. Atributy jsou _public_ (nikoliv _private_) kvůli snadnějšímu přístupu. Atributy jsou označeny jako _final_ (konstanty), což znamená, že se jim smí přiřadit hodnota pouze jednou - typicky v konstruktoru. Tím se přepravka stává neměnným objektem (_immutable_)

Výhodou je, že se na hodnoty v přepravce lze po celou dobu jejího života spolehnout - po nastavení je již nikdo nemůže změnit.

K atributům se přistupuje následnovně: _odkazNaPřepravku.jménoAtributu_.

Ukázka v kódu:

```
class Prepravka {
    public final int x;
    public final int y;

    /**
     * Konstruktor přepravky
     * @param x X-ovy parametr
     * @param y Y-ovy parametr
     */
    public Prepravka(int x, int y) {
      this.x = x;
      this.y = y;
    }

    /**============Konec třídy přepravka===============*/

    /** V jiné třídě budeme naši přepravku používat následujícím způsobem: */
    public static void main(String[] args) {
      Prepravka p = new Prepravka(10, 5);
      System.out.println("X: " + p.x + ", Y: " + p.y);
    }
}

```