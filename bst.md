
class Node:
    def __init__(self, data):
        self.data = data
        self.left = None
        self.right = None


class BinaryTree:
    def __init__(self):
        self.root = None

    def insert(self, data):
        self.root = self._insert_recursive(self.root, data)

    def _insert_recursive(self, node, data):
        if node is None:
            return Node(data)

        if data < node.data:
            node.left = self._insert_recursive(node.left, data)
        else:
            node.right = self._insert_recursive(node.right, data)

        return node

    def inorder(self, node):
        if node:
            self.inorder(node.left)
            print(node.data, end=" ")
            self.inorder(node.right)

    def preorder(self, node):
        if node:
            print(node.data, end=" ")
            self.preorder(node.left)
            self.preorder(node.right)

    def postorder(self, node):
        if node:
            self.postorder(node.left)
            self.postorder(node.right)
            print(node.data, end=" ")

    def search(self, node, data):
        if node is None:
            return False

        if node.data == data:
            return True

        if data < node.data:
            return self.search(node.left, data)

        return self.search(node.right, data)


if __name__ == "__main__":
    tree = BinaryTree()
    tree.insert(50)
    tree.insert(30)
    tree.insert(70)
    tree.insert(20)
    tree.insert(40)
    tree.insert(60)
    tree.insert(80)

    print("Inorder:   ", end="")
    tree.inorder(tree.root)
    print()

    print("Preorder:  ", end="")
    tree.preorder(tree.root)
    print()

    print("Postorder: ", end="")
    tree.postorder(tree.root)
    print()

    print(f"Search 40: {'Found' if tree.search(tree.root, 40) else 'Not Found'}")
    print(f"Search 45: {'Found' if tree.search(tree.root, 45) else 'Not Found'}")

///////////////////////////////////////////////////
<?php

class Node {
    public $data;
    public $left;
    public $right;

    public function __construct($data) {
        $this->data = $data;
        $this->left = null;
        $this->right = null;
    }
}

class BinaryTree {
    public $root;

    public function __construct() {
        $this->root = null;
    }

    public function insert($data) {
        $this->root = $this->insertRecursive($this->root, $data);
    }

    private function insertRecursive($node, $data) {
        if ($node === null) {
            return new Node($data);
        }

        if ($data < $node->data) {
            $node->left = $this->insertRecursive($node->left, $data);
        } else {
            $node->right = $this->insertRecursive($node->right, $data);
        }

        return $node;
    }

    public function inorder($node) {
        if ($node !== null) {
            $this->inorder($node->left);
            echo $node->data . " ";
            $this->inorder($node->right);
        }
    }

    public function preorder($node) {
        if ($node !== null) {
            echo $node->data . " ";
            $this->preorder($node->left);
            $this->preorder($node->right);
        }
    }

    public function postorder($node) {
        if ($node !== null) {
            $this->postorder($node->left);
            $this->postorder($node->right);
            echo $node->data . " ";
        }
    }

    public function search($node, $data) {
        if ($node === null) {
            return false;
        }

        if ($node->data === $data) {
            return true;
        }

        if ($data < $node->data) {
            return $this->search($node->left, $data);
        }

        return $this->search($node->right, $data);
    }
}

$tree = new BinaryTree();
$tree->insert(50);
$tree->insert(30);
$tree->insert(70);
$tree->insert(20);
$tree->insert(40);
$tree->insert(60);
$tree->insert(80);

echo "Inorder:   ";
$tree->inorder($tree->root);
echo "\n";

echo "Preorder:  ";
$tree->preorder($tree->root);
echo "\n";

echo "Postorder: ";
$tree->postorder($tree->root);
echo "\n";

echo "Search 40: " . ($tree->search($tree->root, 40) ? "Found" : "Not Found") . "\n";
echo "Search 45: " . ($tree->search($tree->root, 45) ? "Found" : "Not Found") . "\n";

///////////////////////////////////////
class Node {
    constructor(data) {
        this.data = data;
        this.left = null;
        this.right = null;
    }
}

class BinaryTree {
    constructor() {
        this.root = null;
    }

    insert(data) {
        this.root = this.insertRecursive(this.root, data);
    }

    insertRecursive(node, data) {
        if (node === null) {
            return new Node(data);
        }

        if (data < node.data) {
            node.left = this.insertRecursive(node.left, data);
        } else {
            node.right = this.insertRecursive(node.right, data);
        }

        return node;
    }

    inorder(node) {
        if (node !== null) {
            this.inorder(node.left);
            process.stdout.write(node.data + " ");
            this.inorder(node.right);
        }
    }

    preorder(node) {
        if (node !== null) {
            process.stdout.write(node.data + " ");
            this.preorder(node.left);
            this.preorder(node.right);
        }
    }

    postorder(node) {
        if (node !== null) {
            this.postorder(node.left);
            this.postorder(node.right);
            process.stdout.write(node.data + " ");
        }
    }

    search(node, data) {
        if (node === null) {
            return false;
        }

        if (node.data === data) {
            return true;
        }

        if (data < node.data) {
            return this.search(node.left, data);
        }

        return this.search(node.right, data);
    }
}

// Usage
const tree = new BinaryTree();
tree.insert(50);
tree.insert(30);
tree.insert(70);
tree.insert(20);
tree.insert(40);
tree.insert(60);
tree.insert(80);

console.log("Inorder:   ");
tree.inorder(tree.root);
console.log("\n");

console.log("Preorder:  ");
tree.preorder(tree.root);
console.log("\n");

console.log("Postorder: ");
tree.postorder(tree.root);
console.log("\n");

console.log("Search 40: " + (tree.search(tree.root, 40) ? "Found" : "Not Found"));
console.log("Search 45: " + (tree.search(tree.root, 45) ? "Found" : "Not Found"));