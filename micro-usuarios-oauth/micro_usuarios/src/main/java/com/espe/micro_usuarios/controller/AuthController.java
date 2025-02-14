package com.espe.micro_usuarios.controller;

import com.espe.micro_usuarios.models.entities.Usuario;
import com.espe.micro_usuarios.security.JwtTokenProvider;
import com.espe.micro_usuarios.services.UsuarioService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.web.bind.annotation.*;

import java.util.HashMap;
import java.util.Map;
import java.util.List;
import java.util.Date;

@RestController
@RequestMapping("/api/auth")
public class AuthController {
 
    @Autowired
    private AuthenticationManager authenticationManager;

    @Autowired
    private UsuarioService usuarioService;

    @Autowired
    private PasswordEncoder passwordEncoder;

    @Autowired
    private JwtTokenProvider tokenProvider;

    @PostMapping("/login")
    public ResponseEntity<?> authenticateUser(@RequestBody LoginDto loginDto) {
        Authentication authentication = authenticationManager.authenticate(
                new UsernamePasswordAuthenticationToken(loginDto.getEmail(), loginDto.getPassword()));

        SecurityContextHolder.getContext().setAuthentication(authentication);

        String token = tokenProvider.generateToken(authentication);
        Usuario usuario = usuarioService.findByEmail(loginDto.getEmail()).orElseThrow();

        Map<String, Object> response = new HashMap<>();
        response.put("token", token);
        response.put("tokenType", "Bearer");
        response.put("role", usuario.getRole());

        return ResponseEntity.ok(response);
    }

    @PostMapping("/register")
    public ResponseEntity<?> registerUser(@RequestBody RegistroDto registroDto) {
        if (usuarioService.existsByEmail(registroDto.getEmail())) {
            Map<String, String> response = new HashMap<>();
            response.put("message", "Email ya está registrado");
            return ResponseEntity.badRequest().body(response);
        }

        Usuario usuario = new Usuario();
        usuario.setNombre(registroDto.getNombre());
        usuario.setApellido(registroDto.getApellido());
        usuario.setEmail(registroDto.getEmail());
        usuario.setPassword(passwordEncoder.encode(registroDto.getPassword()));
        usuario.setTelefono(registroDto.getTelefono());
        usuario.setFechaNacimiento(registroDto.getFechaNacimiento());
        
        // Si es el primer usuario, asignarle rol de ADMIN
        if (((List<Usuario>)usuarioService.findAll()).isEmpty()) {
            usuario.setRole(Usuario.Role.ADMIN);
        } else {
            usuario.setRole(Usuario.Role.USER);
        }

        Usuario savedUser = usuarioService.save(usuario);

        Map<String, String> response = new HashMap<>();
        response.put("message", "Usuario registrado exitosamente");
        response.put("role", savedUser.getRole().toString());
        return ResponseEntity.status(HttpStatus.CREATED).body(response);
    }
}

class LoginDto {
    private String email;
    private String password;

    // Getters y Setters
    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }
}

class RegistroDto {
    private String nombre;
    private String apellido;
    private String email;
    private String password;
    private String telefono;
    private Date fechaNacimiento;

    // Getters y Setters
    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public String getApellido() {
        return apellido;
    }

    public void setApellido(String apellido) {
        this.apellido = apellido;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }

    public String getTelefono() {
        return telefono;
    }

    public void setTelefono(String telefono) {
        this.telefono = telefono;
    }

    public Date getFechaNacimiento() {
        return fechaNacimiento;
    }

    public void setFechaNacimiento(Date fechaNacimiento) {
        this.fechaNacimiento = fechaNacimiento;
    }
} 