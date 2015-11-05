# Stavitel (Builder)

V případě velmi složitý instance umožňuje její postupný vytváření
- vytváříme jen to, co nás ve skutečnosti zajímá, ostatní je _default_
- navíc mohou být jednotlivý části velmi složitý a proto je vytváří vždy specializovaná metoda

Mějme následující výčtový typ:

```
public enum ZnalostJazyka {
    ZADNA, A1, A2, B1, B2, C1, C2, RODILY_MLUVCI;
}

```

Dále budeme mít studenta jazykový školy, který musí mít nainicializovaný všechny atributy (znalosti všech uvedených jazyků)
- pro malý počet jazyků by šel jejich seznam předat jako parametry konstruktoru
- v případě, že by škola vyučovala desítky jazyků, by byl konstruktor nepřehledný

```
public class StudentJazykoveSkoly {
    private final String jmeno; // musí být vyplněno
    private ZnalostJazyka cestina;
    private ZnalostJazyka anglictina;
    private ZnalostJazyka nemcina;
    private ZnalostJazyka rustina;

    private StudentJazykoveSkoly(Builder builder) {
        this.jmeno = builder.jmeno;
        this.cestina = builder.cestina;
        this.anglictina = builder.anglictina;
        this.nemcina = builder.nemcina;
        this.rustina = builder.rustina;
    }

    public static class Builder {
        public final String jmeno;
        public ZnalostJazyka cestina = ZnalostJazyka.ZADNA;
        public ZnalostJazyka anglictina = ZnalostJazyka.ZADNA;

        public ZnalostJazyka nemcina = ZnalostJazyka.ZADNA;
        public ZnalostJazyka rustina = ZnalostJazyka.ZADNA;

        // vytvoření vnější třídy
        public StudentJazykoveSkoly build() {
            return new StudentJazykoveSkoly(this);
        }

        // konstruktor pro povinné atributy
        public Builder(String jmeno) {
            this.jmeno = jmeno;
        }

        public Builder cestina(ZnalostJazyka uroven) {
            this.cestina = uroven;
            return this;
        }

        public Builder anglictina(ZnalostJazyka uroven) {
            this.anglictina = uroven;
            return this;
        }

        public Builder nemcina(ZnalostJazyka uroven) {
            this.nemcina = uroven;
            return this;
        }

        public Builder rustina(ZnalostJazyka uroven) {
            this.rustina = uroven;
            return this;
        }
    }
}

```

Postupná konstrukce je umožněna díky vnořený třídě (má přístup k atributům vnější třídy) _Builder_
- ta svojí metodou _build()_ vytvoří a vrátí objekt svý vnější třídy _StudentJazykoveSkoly_
- vyvolá konstruktor vnější třídy ve kterým se pouze překopíruje aktuální nastavení ze třídy _Builder_

Konstruktor třídy _Builder_ mí jen jeden parametr - řetězec jmýno studenta, který musí být vždy zadáno

Specializovaný metody (_cestina(),...) umožní samostatnou inicializaci kazdýho jednotlivýho jazyka_
- _vždy vrátí objekt třídy_ Builder_, což pak umožňuje zřetězení jednotlivých jazyků_
- _V aplikaci se jednotliví studneti vytvářejí postupným skládáním jen významových jazyků._
_Celý proces je završen voláním metody_ build()

```
public class Aplikace {
    public static void main(String[] args) {
      StudentJazykoveSkoly pavel = new StudentJazykoveSkoly.Builder("Pavel")
                                    .cestina(ZnalostJazyka.RODILY_MLUVCI)
                                    .anglictina(ZnalostJazyka.B2)
                                    .rustina(ZnalostJazyka.B2)
                                    .build();
      StudentJazykoveSkoly keith = new StudentJazykoveSkoly.Builder("Keith")
                                    .anglictina(ZnalostJazyka.RODILY_MLUVCI)
                                    .nemcina(ZnalostJazyka.C1)
                                    .rustina(ZnalostJazyka.ZADNA)
                                    .cestina(ZnalostJazyka.A1)
                                    .build();
    }
}

```