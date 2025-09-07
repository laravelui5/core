# Website laravelui5.com

run with `npm run docs:dev`.

---
---

# 1) Stabiler JSON-Vertrag (für Types/Bindings)

Definiere dir einmalig Interfaces (TS oder JSDoc). So bleiben Bindings robust – gerade bei Geldfeldern als Strings.

```ts
// Geld und Zahlen bleiben als string (Backend runden/formatieren)
type Money = string;

interface SettlementPositionLine {
  position_id: number;
  rate: Money;
  hours: Money;
  amount: Money;
}

interface SettlementPosition {
  settlement_position_id: number;
  title: string;
  description: string | null;
  gross_current: Money;
  discount_percent: Money;
  discount_amount: Money;
  net_current: Money | null;      // kann null sein, s. Robustheit
  gross_to_date: Money | null;
  document_date: string | null;   // 'YYYY-MM-DD'
  lines: SettlementPositionLine[];
}

interface Settlement {
  id: number;
  total_net: Money;
  total_tax: Money;
  total_gross: Money;
  total_pre_skonto: Money;
  skonto: Money;
  settled_at: string | null;
  period_from: string | null;
  period_to: string | null;
  invoice_no: string | null;
  invoice_date: string | null;
  positions: SettlementPosition[];
  // order: ... (optional tippen)
}

interface SettlementResponse {
  data: { settlement: Settlement };
}
```

# 2) UI5: Laden & Binden in 60 Sekunden

Controller (JS/TS) – einfache JSONModel-Variante:

```js
import JSONModel from "sap/ui/model/json/JSONModel";

export default {
  onInit() {
    const m = new JSONModel();
    this.getView().setModel(m, "settlement");
    // Beispiel-URL anpassen:
    m.loadData("/api/v1/settlements/123/kpis"); 
  }
};
```

XML-View (Liste der Positionen; Zahlendarstellung bewusst simpel gehalten):

```xml
<List id="posList" items="{settlement>/data/settlement/positions}">
  <ObjectListItem
    title="{settlement>title}"
    number="{settlement>net_current}"
    numberUnit="€">
    <attributes>
      <ObjectAttribute text="Brutto kumuliert: {settlement>gross_to_date} €"/>
      <ObjectAttribute text="Rabatt: {settlement>discount_percent} %"/>
      <ObjectAttribute text="Belegdatum: {settlement>document_date}"/>
    </attributes>
  </ObjectListItem>
</List>
```

Optional (Lines aufklappen):

```xml
<ObjectListItem ... >
  <attributes>
    <!-- ... -->
  </attributes>
  <content>
    <Table items="{settlement>lines}">
      <columns>
        <Column><Text text="Position"/></Column>
        <Column><Text text="Rate"/></Column>
        <Column><Text text="Stunden"/></Column>
        <Column><Text text="Betrag"/></Column>
      </columns>
      <items>
        <ColumnListItem>
          <ObjectIdentifier title="{settlement>position_id}"/>
          <ObjectNumber number="{settlement>rate}" unit="€"/>
          <ObjectNumber number="{settlement>hours}"/>
          <ObjectNumber number="{settlement>amount}" unit="€"/>
        </ColumnListItem>
      </items>
    </Table>
  </content>
</ObjectListItem>
```

**Tipp:** Wenn du Variante A (Summen pro `order_position_id`) gewählt hast, benenne die Felder klar (`net_current`, `gross_to_date`) und halte die Pfade stabil wie oben – das spart dir späteres Refactoring im View.

# 3) „Contract Test“ in Pest (schützt dich vor stillen Regressions)

Ein schlanker Test, der die wichtigsten Felder & Typen abklopft – die konkreten Werte sind Seed-abhängig, aber du kannst z. B. auf Struktur und ein paar Kernpfade testen:

```php
it('returns settlement KPIs in stable shape', function () {
    // Arrange: Factory/Seeder für Settlement, Positions, Lines …

    $response = $this->getJson("/api/v1/settlements/{$settlement->id}/kpis");

    $response->assertOk()
        ->assertJson(fn (\Illuminate\Testing\Fluent\AssertableJson $json) =>
            $json->has('data.settlement', fn ($s) =>
                $s->whereType('id', 'integer')
                  ->whereType('total_net', 'string')
                  ->whereType('total_tax', 'string')
                  ->has('positions', fn ($p) =>
                      $p->each(fn ($pos) =>
                          $pos->whereType('settlement_position_id', 'integer')
                              ->whereType('net_current', 'string|null')
                              ->whereType('gross_to_date', 'string|null')
                              ->has('lines')
                      )
                  )
            )
        );
});
```
