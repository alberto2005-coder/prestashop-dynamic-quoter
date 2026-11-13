# Dynamic 3D Quoter para PrestaShop

[![PrestaShop](https://img.shields.io/badge/PrestaShop-1.7%20%7C%208.x%20%7C%209.x-DF0067?logo=prestashop&logoColor=white)](https://www.prestashop-project.org/)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MIT](https://img.shields.io/badge/Licencia-MIT-green)](LICENSE)

[Español](#español) · [English](#english)

---

## Español

### Índice

- [Descripción](#descripción)
- [Características](#características)
- [Compatibilidad](#compatibilidad)
- [Cómo funciona](#cómo-funciona)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Pruebas](#pruebas)
- [Seguridad](#seguridad)
- [Licencia](#licencia)

### Descripción

Módulo de **PrestaShop** que calcula el precio de un producto en tiempo real a partir de parámetros que introduce el cliente: dimensiones, densidad de relleno y material. Está pensado para negocios de **fabricación bajo demanda** e **impresión 3D**, con previsualización visual y cálculo por AJAX antes de añadir al carrito.

### Características

- **Cálculo dinámico por AJAX**, sin recargar la página.
- **Sliders** para dimensiones y densidad.
- **Previsualización** que escala en tiempo real según las medidas.
- **Materiales con multiplicador**: PLA, ABS y PETG.
- **Persistencia**: los parámetros se guardan y aparecen en el pedido, en el frontend y en el backoffice.
- **Override seguro** del precio mediante el sistema de overrides de PrestaShop.
- **Validación reforzada**: límites de densidad (0–100 %), dimensiones finitas y lista blanca de materiales.

### Compatibilidad

| PrestaShop | Ciclo de vida | PHP compatible | PHP recomendado | Estado |
|---|---|---|---|---|
| 9.0.x | Activo | 8.1, 8.2, 8.3, 8.4 | 8.2 / 8.3 | Compatible |
| 8.2.x | Soporte activo | 8.1, 8.2, 8.3 | 8.2 / 8.3 | Soporte oficial |
| 8.1.x | Soporte activo (LTS) | 8.1, 8.2 | 8.1 / 8.2 | Soporte oficial |
| 1.7.8.x | EOL | 7.4 | 7.4 (legacy) | Modo legacy |

PHP 8.0 y anteriores están fuera de soporte: usa 8.1 o superior siempre que puedas.

### Cómo funciona

1. El cliente ajusta los parámetros en `product_fields.tpl`.
2. `front.js` actualiza la previsualización y envía una petición AJAX a `ajax.php`.
3. El controlador valida los datos, aplica las fórmulas y devuelve el precio formateado.
4. El precio se guarda en la cookie y en la tabla `ps_dynamicprice_info`.
5. Al añadir al carrito, el override de `Cart.php` aplica el coste dinámico.

### Instalación

1. Descarga el repositorio como `.zip`.
2. En el backoffice: **Módulos → Gestor de módulos → Subir un módulo**.
3. Sube el `.zip` y pulsa **Instalar**.
4. Configura los costes base y los multiplicadores en la página del módulo.

> [!NOTE]
> El ZIP debe contener la carpeta del módulo (`dynamicprice/`) en la raíz, no los archivos sueltos. Si PrestaShop no lo reconoce, comprueba esa estructura antes de volver a subirlo.

### Configuración

Desde la configuración del módulo defines:

- **Coste base** por pieza.
- **Precio por volumen** y por gramo, según tu modelo de negocio.
- **Multiplicadores** de material (PLA, ABS, PETG).
- **Límites** de dimensiones y densidad.

Ajusta las fórmulas a tus costes reales antes de publicar el módulo en una tienda en producción.

### Pruebas

```bash
php tests/test_compatibility_and_security.php
```

La suite comprueba la compatibilidad de versiones de PHP y las reglas de validación: rangos de densidad, dimensiones finitas, lista blanca de materiales y comparación segura de tokens.

### Seguridad

- **CSRF**: comparación con `hash_equals` en tiempo constante.
- **Validación de precios**: evita valores nulos, negativos o fuera de rango.
- **XSS**: escapado de variables en las plantillas Smarty.
- **Materiales**: solo se aceptan `pla`, `abs` y `petg`.

Aun así, revisa el módulo con tus propios datos y prueba el flujo completo en una tienda de staging antes de instalarlo en producción.

### Licencia

**MIT** — consulta [LICENSE](LICENSE).

---

## English

### Table of contents

- [Overview](#overview)
- [Features](#features)
- [Compatibility](#compatibility)
- [How it works](#how-it-works)
- [Installation](#installation)
- [Configuration](#configuration)
- [Testing](#testing)
- [Security](#security)
- [License](#license)

### Overview

A **PrestaShop** module that calculates a product's price in real time from customer-supplied parameters: dimensions, infill density and material. It targets **made-to-order manufacturing** and **3D printing** businesses, with a visual preview and AJAX calculation before adding to the cart.

### Features

- **Dynamic AJAX calculation** without reloading the page.
- **Sliders** for dimensions and density.
- **Preview** that scales in real time with the measurements.
- **Material multipliers**: PLA, ABS and PETG.
- **Persistence**: parameters are stored and shown on the order, in the frontend and in the back office.
- **Safe price override** through PrestaShop's override system.
- **Hardened validation**: density bounds (0–100 %), finite dimensions and a material whitelist.

### Compatibility

| PrestaShop | Lifecycle | Supported PHP | Recommended PHP | Status |
|---|---|---|---|---|
| 9.0.x | Active | 8.1, 8.2, 8.3, 8.4 | 8.2 / 8.3 | Compatible |
| 8.2.x | Active support | 8.1, 8.2, 8.3 | 8.2 / 8.3 | Officially supported |
| 8.1.x | Active support (LTS) | 8.1, 8.2 | 8.1 / 8.2 | Officially supported |
| 1.7.8.x | EOL | 7.4 | 7.4 (legacy) | Legacy mode |

PHP 8.0 and earlier are end-of-life: use 8.1 or later whenever possible.

### How it works

1. The customer adjusts the parameters in `product_fields.tpl`.
2. `front.js` updates the preview and sends an AJAX request to `ajax.php`.
3. The controller validates the data, applies the formulas and returns the formatted price.
4. The price is stored in a cookie and in the `ps_dynamicprice_info` table.
5. When adding to the cart, the `Cart.php` override applies the dynamic cost.

### Installation

1. Download the repository as a `.zip`.
2. In the back office: **Modules → Module Manager → Upload a module**.
3. Upload the `.zip` and click **Install**.
4. Configure base costs and multipliers on the module page.

> [!NOTE]
> The ZIP must contain the module folder (`dynamicprice/`) at its root, not loose files. If PrestaShop does not recognise it, check that structure before uploading again.

### Configuration

The module settings let you define:

- **Base cost** per piece.
- **Price per volume** and per gram, according to your business model.
- **Material multipliers** (PLA, ABS, PETG).
- **Dimension and density limits**.

Adjust the formulas to your real costs before publishing the module on a production store.

### Testing

```bash
php tests/test_compatibility_and_security.php
```

The suite checks PHP version compatibility and the validation rules: density ranges, finite dimensions, material whitelist and constant-time token comparison.

### Security

- **CSRF**: constant-time comparison with `hash_equals`.
- **Price validation**: rejects null, negative or out-of-range values.
- **XSS**: escaped variables in Smarty templates.
- **Materials**: only `pla`, `abs` and `petg` are accepted.

Even so, review the module with your own data and run the full flow on a staging store before installing it in production.

### License

**MIT** — see [LICENSE](LICENSE).
