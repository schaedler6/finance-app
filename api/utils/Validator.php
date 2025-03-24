<?php
/**
 * Validator.php
 * Classe responsável pela validação de dados dos formulários
 */
class Validator {
    /**
     * Valida um valor monetário 
     * Aceita formatos como "1.000,00", "1000,00", "1000.00", "1000"
     */
    public static function validarMoeda($valor) {
        // Remove espaços em branco
        $valor = trim($valor);
        
        // Verifica se está vazio
        if (empty($valor)) {
            return false;
        }
        
        // Remove pontos e substitui vírgula por ponto para o formato americano
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
        
        // Verifica se é um número válido
        if (!is_numeric($valor) || $valor < 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Formata um valor para armazenamento no banco como decimal
     */
    public static function formatarMoeda($valor) {
        // Remove pontos de milhar e converte vírgula para ponto
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
        
        // Garante 2 casas decimais
        return number_format((float)$valor, 2, '.', '');
    }
    
    /**
     * Valida uma data no formato YYYY-MM-DD
     */
    public static function validarData($data) {
        if (empty($data)) {
            return false;
        }
        
        $formato = 'Y-m-d';
        $d = DateTime::createFromFormat($formato, $data);
        
        // Verifica se a data é válida e se o formato está correto
        return $d && $d->format($formato) === $data;
    }
    
    /**
     * Valida se um campo de texto não está vazio e tem tamanho adequado
     */
    public static function validarTexto($texto, $minLength = 3, $maxLength = 255) {
        $texto = trim($texto);
        $length = strlen($texto);
        
        return !empty($texto) && $length >= $minLength && $length <= $maxLength;
    }
    
    /**
     * Valida se um ID é um número inteiro positivo
     */
    public static function validarId($id) {
        return is_numeric($id) && $id > 0 && floor($id) == $id;
    }
    
    /**
     * Retorna um array de erros com base nos dados fornecidos
     */
    public static function validarTransacao($dados) {
        $erros = [];
        
        // Valida categoria_id
        if (!isset($dados['categoria_id']) || !self::validarId($dados['categoria_id'])) {
            $erros['categoria_id'] = 'Categoria inválida';
        }
        
        // Valida descrição
        if (!isset($dados['descricao']) || !self::validarTexto($dados['descricao'], 3, 100)) {
            $erros['descricao'] = 'A descrição deve ter entre 3 e 100 caracteres';
        }
        
        // Valida valor
        if (!isset($dados['valor']) || !self::validarMoeda($dados['valor'])) {
            $erros['valor'] = 'Valor inválido. Use o formato 0,00 ou 0.00';
        }
        
        // Valida data
        if (!isset($dados['data']) || !self::validarData($dados['data'])) {
            $erros['data'] = 'Data inválida. Use o formato YYYY-MM-DD';
        }
        
        // Valida tipo
        if (!isset($dados['tipo']) || !in_array($dados['tipo'], ['receita', 'despesa'])) {
            $erros['tipo'] = 'Tipo inválido. Deve ser "receita" ou "despesa"';
        }
        
        return $erros;
    }
    
    /**
     * Filtra input para evitar injeção XSS
     */
    public static function sanitizar($input) {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = self::sanitizar($value);
            }
            return $input;
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valida um email
     */
    public static function validarEmail($email) {
        $email = trim($email);
        if (empty($email)) {
            return false;
        }
        
        // Usa a função filter_var para validar o email
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Valida uma senha
     * @param string $senha A senha a ser validada
     * @param int $minLength Comprimento mínimo (padrão: 6)
     * @return bool Verdadeiro se a senha for válida
     */
    public static function validarSenha($senha, $minLength = 6) {
        return !empty($senha) && strlen($senha) >= $minLength;
    }
    
    /**
     * Verifica se o valor é um número inteiro positivo dentro de um intervalo
     */
    public static function validarInteiro($valor, $min = 1, $max = PHP_INT_MAX) {
        if (!is_numeric($valor)) {
            return false;
        }
        
        $intValue = (int)$valor;
        return $intValue == $valor && $intValue >= $min && $intValue <= $max;
    }
    
    /**
     * Valida números de telefone em formato brasileiro
     * Aceita formatos: (XX) XXXXX-XXXX ou (XX) XXXX-XXXX
     */
    public static function validarTelefone($telefone) {
        $telefone = trim($telefone);
        
        // Remove caracteres não numéricos
        $numeros = preg_replace('/\D/', '', $telefone);
        
        // Verifica o tamanho do número (10 ou 11 dígitos)
        return strlen($numeros) >= 10 && strlen($numeros) <= 11;
    }
    
    /**
     * Valida CPF (implementação simplificada)
     */
    public static function validarCPF($cpf) {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/\D/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1+$/', $cpf)) {
            return false;
        }
        
        // Algoritmo de validação do CPF
        // Implementação simplificada para exemplo
        return true;
    }
    
    /**
     * Retorna um array de erros com base nos dados fornecidos para um usuário
     */
    public static function validarUsuario($dados) {
        $erros = [];
        
        // Valida nome
        if (!isset($dados['nome']) || !self::validarTexto($dados['nome'], 3, 100)) {
            $erros['nome'] = 'O nome deve ter entre 3 e 100 caracteres';
        }
        
        // Valida email
        if (!isset($dados['email']) || !self::validarEmail($dados['email'])) {
            $erros['email'] = 'Email inválido';
        }
        
        // Valida senha
        if (isset($dados['senha']) && !self::validarSenha($dados['senha'])) {
            $erros['senha'] = 'A senha deve ter pelo menos 6 caracteres';
        }
        
        return $erros;
    }
} 