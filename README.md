# Prueba Técnica - MiPlante

**Autor:** Emanuel Millán Hernández

---

## A1. Respuesta

El problema que se encuentra en la lógica está en el cálculo del IVA de la fianza, ya que actualmente este IVA se calcula con base en el subtotal, lo cual es incorrecto, porque únicamente debe calcularse sobre el valor total de la fianza. Para resolver este inconveniente, utilizo una variable llamada **$totalFianza**, que se encarga de almacenar el valor total de la fianza para posteriormente calcular el IVA sobre este valor y sumarlo al valor total que se devuelve en el método.

```php
// app/Services/CheckoutService.php
public function calcularTotal(array $items, float $tasaFianza): float
{
    $subtotal = 0;
    $totalFianza = 0;

    foreach ($items as $item) {
	// $item = ['precio' => 1200000, 'cantidad' => 2]
        $valorItem = $item['precio'] * $item['cantidad'];

        $subtotal += $valorItem;
        $totalFianza += $valorItem * $tasaFianza;
    }

    $ivaFianza = $totalFianza * 0.19;

    return $subtotal + $totalFianza + $ivaFianza;
}
```

---

## A2. Respuesta

Sí tiene riesgos a nivel de seguridad, ya que al utilizar la línea: $solicitud->update($request->all()); estamos permitiendo una asignación masiva, haciendo que todo lo que llegue en la petición pueda actualizarse, siempre y cuando esté definido en `$fillable`. Además, teniendo en cuenta que el método únicamente debe actualizar datos de contacto, es mejor impedir la modificación de campos sensibles como:

- `estado`
- `cupo_asignado`
- `rol_usuario`

Para solucionarlo, implementaría una validación que garantice la integridad de los datos y limite la actualización únicamente a los campos permitidos. Incluso, para mantener un código más limpio, utilizaría un Form Request para separar la lógica de validación del controlador.


```php
// app/Http/Controllers/SolicitudController.php
public function update(Request $request, Solicitud $solicitud) 
{ 

    $data = $request->validate([ 
      'nombre' => 'required|string|max:255', 
      'telefono' => 'required|string|max:20', 
      'direccion' => 'required|string|max:255'
    ]); 

    $solicitud->update($data); 

    return back()->with('ok', 'Datos actualizados'); 
}
```

---

## A3. Respuesta

En este caso considero que, para evitar un descuadre de unidades cuando se trata de la última disponible, se puede implementar una transacción a nivel de base de datos utilizando lockForUpdate(). Este método bloquea el registro mientras se validan las unidades disponibles y se actualiza el stock, haciendo que cualquier otra compra del mismo producto espere a que termine la transacción. De esta manera, el stock se mantiene consistente y se evita vender más unidades de las disponibles.

```php
use Illuminate\Support\Facades\DB;

// app/Services/InventarioService.php
public function comprar(int $productoId, int $cantidad): void
{
    DB::transaction(function () use ($productoId, $cantidad) {

        $producto = Producto::lockForUpdate()->find($productoId);

        if ($producto->stock >= $cantidad) {
            // ... procesa el pago ...
            $producto->stock = $producto->stock - $cantidad;
            $producto->save();
        } else {
            throw new \Exception('Sin stock');
        }
    });
}
```

---

## A4. Respuesta

Analizando la lógica mostrada, el problema es que el webhook no realiza ningún tipo de validación del origen de la petición (por ejemplo, mediante un middleware o un token de autenticación), que garantice que realmente proviene de Certicámara. Esto permite que cualquier cliente pueda realizar una petición al endpoint y cambiar el estado de un pagaré a FIRMADO.

Lo ideal sería proteger el endpoint mediante un token o el mecanismo de autenticación que se defina, garantizando que únicamente este proveedor pueda consumir el webhook. Como medida adicional, implementaría el uso de logs para llevar un seguimiento y facilitar la auditoría.


---

## A5. Respuesta

Sí existe un problema de privacidad, ya que se exponen datos sensibles como el score_credito, el cual, desde mi punto de vista, es innecesario para el propósito del log, que únicamente es informar que se está realizando una validación de identidad. También considero innecesario registrar el OTP, ya que, aunque tenga un tiempo de vida limitado, sigue siendo información sensible que no debería quedar almacenada.

```php
// app/Services/IdentidadService.php
Log::info('Validando identidad', [
    'cedula'      => $cliente->cedula,
]);
```

---

## B1. Respuesta

Escogí desarrollar la opción 1, la cual consiste en implementar un endpoint que recibe una lista de cuotas de un crédito y devuelve un resumen con el total a pagar, el número de cuotas y la fecha de la última cuota.

### Endpoint

| Método | Ruta |
|--------|------|
| **POST** | `/api/credito/resumen` |

### Ejemplo de petición

```json
{
    "cuotas": [
        {
            "valor": 100000,
            "fecha": "2026-08-15"
        },
        {
            "valor": 100000,
            "fecha": "2026-09-15"
        },
        {
            "valor": 100000,
            "fecha": "2026-10-15"
        }
    ]
}
```

### Respuesta esperada

```json
{
    "code": 200,
    "message": "Resumen generado correctamente",
    "data": {
        "total_pagar": 300000,
        "numero_cuotas": 3,
        "ultima_cuota": "2026-10-15"
    }
}
```

Para la implementación se utilizó un Form Request para centralizar las validaciones, un Service para encapsular la lógica de negocio, manejo de excepciones y registro de logs. Adicionalmente, se implementaron pruebas automatizadas para validar tanto el caso exitoso como los diferentes escenarios de error por validación.

---

## C4. Respuesta

En este caso buscaría reproducir el escenario en el que ocurre el error de cálculo que genera cobros superiores a algunos clientes, ya que el enunciado indica que no afecta a todos. Haría esta reproducción en un ambiente de pruebas o desarrollo (por ejemplo, desplegando la aplicación de forma local). A partir de ahí, revisaría todos los métodos involucrados en el proceso de cálculo con el fin de identificar exactamente dónde y por qué se está produciendo el error. De esta manera, evitaría modificar código que no tenga relación con el problema y que pudiera afectar otras funcionalidades.

Una vez identificada la causa, realizaría la corrección en la lógica con el menor impacto posible. Posteriormente, probaría nuevamente con los clientes que presentan la novedad para verificar que el problema fue solucionado y, además, ejecutaría pruebas con clientes que no presentan el inconveniente para asegurar que el ajuste no afecte otros escenarios. Finalmente, si todas las validaciones son satisfactorias, desplegaría el cambio al ambiente de producción y realizaría un monitoreo para confirmar que la solución funciona correctamente.

---

## C5. Respuesta

La diferencia entre ambos conceptos es que la autenticación tiene como objetivo validar que un usuario sea quien dice ser, normalmente mediante credenciales como usuario y contraseña. Por otro lado, la autorización tiene como propósito determinar qué acciones puede realizar ese usuario dentro del sistema, de acuerdo con los permisos que tenga asignados. En un sistema como el mencionado, es importante garantizar que la información relacionada con los cupos de crédito solo pueda ser consultada o modificada por los usuarios autorizados, preservando así la seguridad e integridad de los datos.

---

## C6. Respuesta

Las medidas que tomaría serían implementar timeouts en las peticiones para evitar esperas prolongadas que puedan generar saturación en el servidor; además, implementaría una lógica de reintentos para manejar fallos temporales. También realizaría un manejo adecuado de las excepciones, evitando que una falla del servicio externo interrumpa los procesos de la aplicación. Como medidas adicionales, utilizaría logs para facilitar el monitoreo y el diagnóstico de errores y, cuando el proceso lo permita, implementaría colas para procesar las solicitudes de forma asíncrona y mejorar la experiencia del usuario.
