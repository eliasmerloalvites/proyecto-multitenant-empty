<?php

namespace Database\Factories;

use App\Models\Personal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Personal>
 */
class PersonalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
    protected $model = Personal::class;

    public function definition(): array
    {
        return [
            'PER_Nombre' => $this->faker->firstName,
            'PER_Apellido' => $this->faker->lastName,
            'PER_TipoDocumento' => 'DNI',
            'PER_NumeroDocumento' => $this->faker->unique()->numerify('########'),
            'PER_FechaNacimiento' => $this->faker->date(),
            'PER_Edad' => $this->faker->numberBetween(18, 60),
            'PER_Sexo' => $this->faker->randomElement(['MASCULINO', 'FEMENINO']),
            'PER_EstadoCivil' => $this->faker->randomElement(['SOLTERO', 'CASADO']),
            'PER_NumeroHijos' => $this->faker->numberBetween(0, 5),
            'PER_Procedencia' => $this->faker->city,
            'PER_Direccion' => $this->faker->address,
            'PER_Referencia' => $this->faker->sentence,
            'PER_Correo' => $this->faker->unique()->safeEmail,
            'PER_Celular' => $this->faker->numerify('9########'),
            'PER_EstadoLaboral' => 'ACTIVO',
        ];
    }
}
