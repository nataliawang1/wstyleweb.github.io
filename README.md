# W-Style - Sitio Web con Panel de Administración

Sistema de sitio web para W-Style con panel de administración completo con login y funcionalidad CRUD.

## Estructura del Proyecto

```
W-Style/
├── admin/                  # Panel de administración
│   ├── dashboard.php      # Dashboard principal
│   ├── login.php          # Página de login
│   ├── logout.php         # Cerrar sesión
│   ├── sidebar.php        # Barra lateral de navegación
│   ├── portafolio/        # CRUD de portafolio
│   │   ├── index.php
│   │   ├── crear.php
│   │   └── editar.php
│   ├── servicios/         # CRUD de servicios
│   │   ├── index.php
│   │   ├── crear.php
│   │   └── editar.php
│   ├── clientes/          # CRUD de clientes
│   │   ├── index.php
│   │   ├── crear.php
│   │   └── editar.php
│   ├── wclub/             # Lista de miembros W Club
│   │   └── index.php
│   └── contactos/         # Mensajes de contacto
│       └── index.php
├── config/                # Configuración
│   └── database.php      # Conexión a la base de datos
├── includes/              # Archivos incluidos
│   └── auth.php          # Funciones de autenticación
├── css/                   # Estilos
│   ├── style.css         # Estilos del sitio público
│   └── admin-style.css   # Estilos del panel admin
├── images/                # Imágenes del sitio
├── index.html             # Página de inicio
├── wclub.html             # Página W Club
├── portafolio.html        # Página Portafolio
├── servicios.html         # Página Servicios
├── clientes.html          # Página Clientes
├── contacto.html          # Página Contacto
└── database.sql           # Script de base de datos
```

## Instalación

### 1. Requisitos Previos
- XAMPP (Apache + MySQL + PHP)
- Navegador web

### 2. Configuración de la Base de Datos

1. Abre phpMyAdmin: http://localhost/phpmyadmin
2. Crea una nueva base de datos llamada `wstyle_db`
3. Importa el archivo `database.sql` en la base de datos creada

O alternativamente, ejecuta el script SQL directamente en phpMyAdmin:
- Abre el archivo `database.sql`
- Copia todo el contenido
- Pégalo en la pestaña SQL de phpMyAdmin
- Ejecuta el script

### 3. Configuración de la Conexión

El archivo `config/database.php` ya está configurado para XAMPP por defecto:
- Host: localhost
- Usuario: root
- Contraseña: (vacía)
- Base de datos: wstyle_db

Si necesitas cambiar estos valores, edita el archivo `config/database.php`.

### 4. Acceso al Panel de Administración

1. Abre tu navegador y ve a: `http://localhost/W-Style/admin/login.php`
2. Usuario: `admin`
3. Contraseña: `admin123`

## Funcionalidades del Panel de Administración

### Dashboard
- Vista general de estadísticas
- Accesos rápidos a todas las secciones
- Contadores de items en cada sección

### Portafolio (CRUD Completo)
- **Crear**: Agregar nuevos items al portafolio con imagen
- **Leer**: Ver lista de todos los items
- **Actualizar**: Editar items existentes
- **Eliminar**: Borrar items del portafolio

### Servicios (CRUD Completo)
- **Crear**: Agregar nuevos servicios con icono emoji
- **Leer**: Ver lista de todos los servicios
- **Actualizar**: Editar servicios existentes
- **Eliminar**: Borrar servicios

### Clientes (CRUD Completo)
- **Crear**: Agregar nuevos clientes con logo y testimonio
- **Leer**: Ver lista de todos los clientes
- **Actualizar**: Editar clientes existentes
- **Eliminar**: Borrar clientes

### W Club
- Ver lista de miembros registrados
- Eliminar miembros

### Contactos
- Ver mensajes recibidos desde el formulario de contacto
- Marcar mensajes como leídos
- Eliminar mensajes

## Seguridad

- Sistema de login con sesiones PHP
- Contraseñas hasheadas con password_hash()
- Protección de rutas con requireLogin()
- Validación de formularios
- Consultas preparadas para prevenir SQL injection

## Credenciales por Defecto

- **Usuario**: admin
- **Contraseña**: admin123

⚠️ **Importante**: Cambia estas credenciales en producción editando la base de datos directamente.

## Personalización

### Cambiar Contraseña del Administrador

Para cambiar la contraseña del administrador:

1. Genera un nuevo hash de contraseña con PHP:
```php
<?php
echo password_hash('tu_nueva_contraseña', PASSWORD_DEFAULT);
?>
```

2. Actualiza la base de datos en phpMyAdmin:
```sql
UPDATE administradores SET password = 'nuevo_hash_generado' WHERE username = 'admin';
```

### Imágenes

Las imágenes del sitio deben colocarse en la carpeta `images/`. El sistema acepta los siguientes formatos:
- JPG
- JPEG
- PNG
- GIF
- WebP

## Soporte

Para cualquier problema o consulta, verifica:
1. Que XAMPP esté ejecutándose (Apache y MySQL)
2. Que la base de datos `wstyle_db` exista
3. Que el archivo `config/database.php` tenga las credenciales correctas
4. Que los permisos de las carpetas sean correctos

## Licencia

Este proyecto es para uso educativo y comercial.
