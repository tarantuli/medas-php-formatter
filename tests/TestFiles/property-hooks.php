<?php

declare(strict_types=1);

// =============================================================================
// PHP 8.4 Property Hooks - Comprehensive Syntax Test File
// =============================================================================
// =============================================================================
// 1. BASIC HOOKS - long form (block body)
// =============================================================================
class BasicLongFormHooks
{
    public string $name {
        get {
            return $this->name;
        }
        set {
            $this->name = $value;
        }
    }

    public int $age {
        get {
            return $this->age;
        }
        set {
            $this->age = $value;
        }
    }
}

// =============================================================================
// 2. BASIC HOOKS - short form (arrow body)
// =============================================================================
class BasicShortFormHooks
{
    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public int $age {
        get => $this->age;
        set => $this->age = $value;
    }
}

// =============================================================================
// 3. GET-ONLY (virtual/computed properties, no backing store)
// =============================================================================
class GetOnlyHooks
{
    // Long form
    public string $fullName {
        get {
            return $this->firstName . ' ' . $this->lastName;
        }
    }

    // Short form
    public string $initials {
        get => strtoupper($this->firstName[0]) . strtoupper($this->lastName[0]);
    }

    // Computed from other properties
    public int $age {
        get => (int) date('Y') - $this->birthYear;
    }

    public function __construct(
        private string $firstName,
        private string $lastName,
        private int    $birthYear,
    )
    {
    }
}

// =============================================================================
// 4. SET-ONLY
// =============================================================================
class SetOnlyHooks
{
    private string $storedName = '';

    // Long form
    public string $name {
        set {
            $this->storedName = strtolower($value);
        }
    }

    // Short form
    public string $title {
        set => $this->storedTitle = trim($value);
    }

    private string $storedTitle = '';
}

// =============================================================================
// 5. HOOKS WITH TYPED $value PARAMETER
// =============================================================================
class TypedValueParameter
{
    public int $score {
        set(int $value) {
            $this->score = max(0, min(100, $value));
        }
    }

    public string $email {
        set(string $value) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Invalid email: $value");
            }

            $this->email = $value;
        }
    }

    // Short form with typed $value
    public float $temperature {
        set(float $value) => $this->temperature = round($value, 2);
    }

    public string $slug {
        set(string $value) => $this->slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $value));
    }
}

// =============================================================================
// 6. DEFAULT VALUES
// =============================================================================
class WithDefaultValues
{
    public string $name = 'Anonymous' {
        get => strtoupper($this->name);
        set => $this->name = trim($value);
    }

    public int $count = 0 {
        set {
            if ($value < 0) {
                throw new InvalidArgumentException('Count cannot be negative');
            }

            $this->count = $value;
        }
    }

    // Get-only with default
    public string $status = 'active' {
        get => ucfirst($this->status);
    }
}

// =============================================================================
// 7. MIXED SHORT AND LONG FORM IN SAME PROPERTY
// =============================================================================
class MixedForms
{
    public string $name {
        get => strtoupper($this->name);
        set {
            $trimmed = trim($value);

            if ($trimmed === '') {
                throw new InvalidArgumentException('Name cannot be empty');
            }

            $this->name = $trimmed;
        }
    }

    public int $quantity {
        get {
            return max(0, $this->quantity);
        }
        set => $this->quantity = $value;
    }
}

// =============================================================================
// 8. READONLY PROPERTIES (hooks are NOT allowed on readonly — shown as contrast)
// =============================================================================
class ReadonlyProperties
{
    // readonly properties use a single constructor-assignment pattern;
    // they cannot have hooks. Shown here as a contrast to hooked properties.
    public readonly string $name;
    public readonly int $id;

    public function __construct(string $name, int $id)
    {
        $this->name = $name;
        $this->id = $id;
    }
}

// =============================================================================
// 9. PROMOTED CONSTRUCTOR PROPERTIES WITH HOOKS
// =============================================================================
class PromotedWithHooks
{
    public function __construct(
        public string $name {
            get => ucfirst($this->name);
            set => $this->name = trim($value);
        },
        public int    $age = 0 {
            set {
                if ($value < 0 || $value > 150) {
                    throw new InvalidArgumentException("Invalid age: $value");
                }

                $this->age = $value;
            }
        },
        public string $id = '' {
            get => strtoupper($this->id);
        },
    )
    {
    }
}

// =============================================================================
// 10. PROMOTED WITH TYPED $value AND DEFAULT
// =============================================================================
class PromotedTypedValueWithDefault
{
    public function __construct(
        public string $email = 'user@example.com' {
            set(string $value) {
                if (!str_contains($value, '@')) {
                    throw new InvalidArgumentException('Invalid email');
                }

                $this->email = strtolower($value);
            }
        },
        public float  $price = 0.0 {
            get => round($this->price, 2);
            set(float $value) => $this->price = abs($value);
        },
    )
    {
    }
}

// =============================================================================
// 11. INTERFACE PROPERTY HOOKS
// =============================================================================
interface WithHookedProperties
{
    // Get-only virtual property in interface
    public string $fullName {
        get;
    }

    // Both hooks declared in interface
    public int $count {
        get;
        set;
    }

    // Set-only
    public string $password {
        set;
    }
}

// =============================================================================
// 12. ABSTRACT CLASS WITH HOOKS
// =============================================================================
abstract class AbstractWithHooks
{
    // Abstract hook - subclass must provide the get body
    abstract public string $label {
        get;
    }

    // Concrete hook in abstract class
    public string $name {
        get => ucfirst($this->name);
        set => $this->name = trim($value);
    }
}

class ConcreteWithHooks extends AbstractWithHooks
{
    public string $label {
        get => 'Concrete: ' . $this->name;
    }
}

// =============================================================================
// 13. INTERFACE IMPLEMENTATION
// =============================================================================
class ImplementsHookedInterface implements WithHookedProperties
{
    private string $firstName = '';
    private string $lastName = '';

    public string $fullName {
        get => $this->firstName . ' ' . $this->lastName;
    }

    public int $count {
        get => $this->count;
        set => $this->count = max(0, $value);
    }

    public string $password {
        set => $this->hashedPassword = password_hash($value, PASSWORD_BCRYPT);
    }

    private string $hashedPassword = '';
}

// =============================================================================
// 14. HOOKS CALLING $this METHODS
// =============================================================================
class HooksWithMethodCalls
{
    public string $email {
        get => $this->email;
        set {
            $this->validateEmail($value);
            $this->email = strtolower($value);
        }
    }

    public array $tags {
        get => $this->tags;
        set {
            $this->tags = array_map(fn(string $tag) => strtolower(trim($tag)), array_unique($value));
        }
    }

    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email: $email");
        }
    }
}

// =============================================================================
// 15. HOOKS REFERENCING OTHER HOOKED PROPERTIES
// =============================================================================
class CrossReferencingHooks
{
    public string $firstName {
        get => $this->firstName;
        set => $this->firstName = ucfirst(strtolower($value));
    }

    public string $lastName {
        get => $this->lastName;
        set => $this->lastName = ucfirst(strtolower($value));
    }

    // Virtual property using other hooked properties
    public string $fullName {
        get => $this->firstName . ' ' . $this->lastName;
    }

    public string $initials {
        get => $this->firstName[0] . '.' . $this->lastName[0] . '.';
    }
}

// =============================================================================
// 16. VISIBILITY MODIFIERS ON HOOKS
// =============================================================================
class HookVisibility
{
    // Public get, protected set — visibility goes on the property declaration
    public protected(set) string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    // Public get, private set
    public private(set) int $id {
        get => $this->id;
        set => $this->id = $value;
    }

    // Public get (short), private set (long)
    public private(set) string $slug {
        get => $this->slug;
        set {
            $this->slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $value));
        }
    }
}

// =============================================================================
// 17. HOOKS IN ENUMS (not supported, but shown for tokenizer edge case)
// Hooks are NOT valid in enums, but backed enum methods that look similar:
// =============================================================================
enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            Status::Active => 'Active',
            Status::Inactive => 'Inactive',
            Status::Pending => 'Pending',
        };
    }
}

// =============================================================================
// 18. ALL VISIBILITY COMBINATIONS ON CLASS PROPERTY
// =============================================================================
class AllVisibilityCombinations
{
    public string $publicBoth {
        get => $this->publicBoth;
        set => $this->publicBoth = $value;
    }

    protected string $protectedBoth {
        get => $this->protectedBoth;
        set => $this->protectedBoth = $value;
    }

    private string $privateBoth {
        get => $this->privateBoth;
        set => $this->privateBoth = $value;
    }

    public protected(set) string $publicGetProtectedSet {
        get => $this->publicGetProtectedSet;
        set => $this->publicGetProtectedSet = $value;
    }

    public private(set) string $publicGetPrivateSet {
        get => $this->publicGetPrivateSet;
        set => $this->publicGetPrivateSet = $value;
    }

    protected private(set) string $protectedGetPrivateSet {
        get => $this->protectedGetPrivateSet;
        set => $this->protectedGetPrivateSet = $value;
    }
}

// =============================================================================
// 19. HOOKS WITH MULTILINE EXPRESSIONS
// =============================================================================
class MultilineExpressions
{
    public array $items {
        get => array_values(
            array_filter($this->items, fn(mixed $item) => $item !== null),
            array_filter($this->items, fn(mixed $item) => $item !== null),
        );
        set {
            $this->items = array_map(
                fn(mixed $item) => is_string($item) ? trim($item) : $item,
                $value,
                array_filter($this->items, fn(mixed $item) => $item !== null),
            );
        }
    }

    public string $description {
        get => implode(
            ' ',
            array_map(
                fn(string $word) => ucfirst($word),
                explode(' a ridiculously long string ', $this->description),
                explode(' a ridiculously long string ', $this->description),
                explode(' a ridiculously long string ', $this->description)
            )
        );
        set => $this->description = trim(
            preg_replace('/\s+/', ' a ridiculously long string ', $value),
            preg_replace('/\s+/', ' a ridiculously long string ', $value),
            preg_replace('/\s+/', ' a ridiculously long string ', $value)
        );
    }
}

// =============================================================================
// 20. STATIC PROPERTIES (hooks not supported, contrast reference)
// =============================================================================
class StaticVsInstance
{
    // Static — no hooks allowed (shown for contrast)
    public static int $instanceCount = 0;

    // Instance — hooks allowed
    public int $value {
        get => $this->value;
        set {
            ++self::$instanceCount;
            $this->value = $value;
        }
    }
}
