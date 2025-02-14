package com.espe.micro_usuarios.services;

import com.espe.micro_usuarios.models.entities.Usuario;
import org.springframework.security.core.userdetails.UserDetailsService;

import java.util.List;
import java.util.Optional;

public interface UsuarioService extends UserDetailsService {
    List<Usuario> findAll();
    Optional<Usuario> findById(Long id);
    Usuario save(Usuario usuario);
    void deleteById(Long id);
    boolean existsByEmail(String email);
    Optional<Usuario> findByEmail(String email);
}