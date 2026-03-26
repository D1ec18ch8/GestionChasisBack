# 📬 Postman Collection - GestionChasis API

## 📋 Contenido

Esta colección incluye todos los endpoints de la API GestionChasis:

- ✅ **Autenticación** (Login, Register, Profile)
- ✅ **Chasis** (CRUD completo)
- ✅ **Tipos de Chasis** (Listar, Crear)
- ✅ **Ubicaciones** (Listar, Crear)
- ✅ **Estados** (Listar, Crear)
- ✅ **Historial** (General, Acciones, Movimientos, PDFs)
- ✅ **Health Check** (Ping)

---

## 🚀 Cómo Importar

### Opción 1: Desde Archivo (Recomendado)

1. **Abre Postman**
2. Click en **"Import"** (esquina superior izquierda)
3. Selecciona la pestaña **"File"**
4. Elige **`GestionChasis-API.postman_collection.json`**
5. Click en **"Import"**

Repite el proceso para el archivo de variables:
- **`GestionChasis-API.postman_environment.json`**

### Opción 2: Copiar y Pegar el JSON

1. Abre `GestionChasis-API.postman_collection.json` con un editor
2. Copia todo el contenido
3. En Postman: Click en **Import** → **"Raw text"** → Pega → **"Import"**

### Opción 3: Desde URL (Si hosting público)

```
No aplica en este caso (archivos locales)
```

---

## ⚙️ Configurar Variables de Entorno

### Automático:
Si importaste `GestionChasis-API.postman_environment.json`, las variables están preconfiguradas.

### Manual:
1. Click en el ícono de ojos (arriba derecha)
2. Haz click en **"Edit"** junto a "GestionChasis - Local Development"
3. Verifica/modifica estas variables:

| Variable | Valor por Defecto |
|----------|------------------|
| `base_url` | `http://localhost:8000` |
| `admin_email` | `admin@gestionchasis.local` |
| `admin_password` | `admin123` |
| `user_email` | `usuario@gestionchasis.local` |
| `user_password` | `usuario123` |
| `token` | (Se llena automáticamente al login) |
| `user_id` | (Se llena automáticamente al login) |

---

## 🔐 Flujo de Autenticación

### Paso 1: Login
1. Expande **"🔐 Autenticación"**
2. Click en **"Login"**
3. Verifica que el body tiene credenciales correctas
4. Click en **"Send"**

**Resultado esperado:**
```json
{
  "message": "Sesión iniciada exitosamente.",
  "user": {...},
  "token": "1|abc123..."
}
```

El token se guarda automáticamente en `{{token}}` gracias al script de test.

### Paso 2: Usar el Token
Todos los endpoints protegidos (excepto Login y Register) automáticamente incluyen:
```
Authorization: Bearer {{token}}
```

No necesitas hacer nada, ¡Postman lo hace por ti! ✨

### Paso 3: Probar Otros Endpoints
Ahora puedes probar cualquier endpoint bajo `auth:sanctum`:
- GET `/api/auth/me`
- GET `/api/chasis`
- POST `/api/chasis`
- etc.

---

## 📝 Ejemplos de Uso

### 1. Registrar Nuevo Usuario
```
Carpeta → 🔐 Autenticación → Registrarse
Body (cambiar email):
{
  "nombre": "Tu Nombre",
  "email": "tu@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### 2. Ver Mi Perfil
```
Carpeta → 🔐 Autenticación → Mi Perfil
(Automáticamente envía Authorization: Bearer {{token}})
```

### 3. Crear un Chasis
```
Carpeta → 🏗️ Chasis → Crear Chasis
Body:
{
  "nombre": "Chasis Nuevo",
  "numero": 123,
  "tipo_chasis_id": 1,
  "ubicacion_id": 1,
  "categoria": "40x20",
  "placa": "XYZ999"
}
```

### 4. Descargar PDF de Movimientos
```
Carpeta → 📊 Historial → Movimientos PDF
(Se descarga el PDF automáticamente)
```

---

## 🆘 Troubleshooting

### "401 Unauthorized"
- ✅ **Solución:** Primero haz Login para obtener el token
- Verifica que `{{token}}` no esté vacío

### "Variable {{token}} is not defined"
- ✅ **Solución:** Importaste la colección pero no las variables
- Importa `GestionChasis-API.postman_environment.json`
- O configura variables manualmente en Environment del Workspace

### "Cannot GET /api/..."
- ✅ **Solución:** La API no está corriendo
- Ejecuta: `php artisan serve`
- Verifica que `{{base_url}}` sea `http://localhost:8000`

### "MySQL Connection Refused"
- ✅ **Solución:** MySQL no está corriendo
- En Laragon: Click en "Start All"
- En XAMPP: Click en "Start" para MySQL

---

## 🎯 Variables Útiles

La colección incluye 3 variables automáticas:

1. **`{{base_url}}`** - URL base de la API
   - Se usa en todos los endpoints
   - Por defecto: `http://localhost:8000`

2. **`{{token}}`** - Token de autenticación
   - Se llena automáticamente después de login
   - Se envía en el header `Authorization: Bearer`

3. **`{{user_id}}`** - ID del usuario autenticado
   - Se llena automáticamente después de login
   - Útil para filtrar por usuario

---

## 📮 Pre-request Scripts

Algunos endpoints incluyen scripts que se ejecutan automáticamente:

### Login - Test Script
```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("token", jsonData.token);
    pm.environment.set("user_id", jsonData.user.id);
    console.log("Token guardado: " + jsonData.token);
}
```

**¿Qué hace?**
- Cuando el login es exitoso (código 200)
- Extrae el token de la respuesta
- Lo guarda en `{{token}}` para usarlo en otros requests
- También guarda el user_id

---

## ✏️ Personalizar la Colección

### Cambiar URL Base
1. Click en el ícono de ojos (variables) 
2. Busca `base_url`
3. Cambia el valor a tu URL (ej: `http://tu-dominio.com`)

### Agregar Más Requests
1. Click derecho en una carpeta
2. "Add Request"
3. Completa URL, método, headers, body
4. Guarda

### Exportar Cambios
Click en los 3 puntitos del lado de la colección → "Export" → Elige versión → Descarga

---

## 🔐 Seguridad

- ✅ Tokens se guardan SOLO en sesión de Postman
- ✅ No se envían a servidores externos
- ✅ Se borran al cerrar Postman
- ✅ Credenciales varían por environment

---

## 📱 Sincronizar Múltiples Dispositivos

### En Postman Cloud:
1. Postman → Settings (esquina superior derecha)
2. "Sync" → Habilitar
3. Login con tu cuenta
4. La colección se sincroniza automáticamente

---

## 🆘 Contacto

Si algo no funciona:
1. Revisa el tab **"Tests"** para errores de scripts
2. Revisa la pestaña **"Console"** (Command + Option + C) para logs
3. Verifica que todos los environment variables estén configurados
4. Prueba con un request sencillo como **"Ping"** primero

---

## 📚 Más Información

- [Documentación de Postman](https://learning.postman.com/)
- [GestionChasis - API Docs](../AUTH_README.md)
- [API Cheatsheet](../API_CHEATSHEET.md)

---

**Última actualización:** Marzo 2025  
**Versión:** 1.0  
**Autenticación:** Sanctum JWT Tokens
