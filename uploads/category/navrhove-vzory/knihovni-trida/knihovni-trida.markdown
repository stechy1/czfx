Je schránka na statické metody a atributy (zejména konstanty). Jako příklad můžeme vzít třídy Math, System, Arrays v JAVA Core API

Pro funkčnost nepotřebuje vytvířet žádné instance, veškerá činnost se odehraje pomocí statických metod a konstant.

Aby nešlo udělat instanci (pokud by to někoho napadlo), udělá se prázdný privátní konstruktor. Navíc se třída označí jako _final_, aby bylo jasné (zejména překladači), že nejde zdědit. Volání metod je velmi rychlé - nemusí se před tím vytvářen instance.

Tato třída se používá velmi často, slouží mimojiné k ukládání konstant platných pro celou aplikaci, např.: adresáře, defaultní hodnoty apod.

```
public final class KnihovniTrida {
    /**
     * privátní konstruktor, aby ebylo možné vytvořit instanci třídy    
     */
    private KnihovniTrida() {}

    /** Ludolfovo číslo */
    public static final double PI = Math.PI;    

    /**
     * Vypočítá obvod kruhu o zadaném poloměru
     *
     * @param polomer Poloměr kruhu    
     * @returns Obvod kruhu
     */
    public static double vypoctiObvodKruhu(double polomer) {
        return 2 * PI * polomer;
    }

    /**============Konec knihovní třídy===============*/

    /** V jiné třídě budeme naši knihovní třídu používat následujícím způsobem: */    
    public static void main(String[] args){    
        double r = 1.0;
        double obvod = KnihovniTrida.vypoctiObvodKruhu(r);
        System.out.println("Obvod kruhu s polomerem: " + r + " je: " + obvod);
    }
}
```