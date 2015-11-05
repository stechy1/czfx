Je třída s vždy jedinou instancí, např. připojení do databáze, otevření I/O proudu.

Znepřístupňuje konstruktor pomocí _private._

Pro získání odkazu používá statickou tovární metodu, která pokaždé vrací referenci na stejnou instanci (odkazů může být samozřejmě víc, ale vždy na jedinou instanci)

```
public class Jedinacek {
   private static Jedinacek INSTANCE = null;
   private Jedinacek() {
      // Privátní konstruktor k zabránění vytvoření instancí.
   }
   public static Jedinacek getINSTANCE() {
      if(INSTANCE == null) {
         INSTANCE = new Jedinacek();
      }
      return INSTANCE;
   }

   // Vypíše na konzoli ahoj
   public void pozdrav() {
      System.out.println("Ahoj");
   }

   //============Konec definice jedináčka===============

   // V jiné třídě budeme singleton používat následujícím způsobem:
   public static void main(String[] args) {
      Jedinacek jedinacek = Jedinacek.getINSTANCE();
      jedinacek.pozdrav();
   }
}

```