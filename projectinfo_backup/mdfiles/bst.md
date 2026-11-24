
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

///////////////=================================================================
Good question—once your BST is fixed, the next natural steps are **search**, **delete**, and **sorting (traversal)**. I’ll show you clean implementations and explain how they work.

---

# ✅ 1. Search in BST

Searching is straightforward:

* Go left if value is smaller
* Go right if value is larger

```php
public function search($data){
    return $this->searchRecursive($this->root, $data);
}

private function searchRecursive($node, $data){
    if($node === null){
        return null;
    }

    if($node->data == $data){
        return $node;
    }

    if($data < $node->data){
        return $this->searchRecursive($node->left, $data);
    } else {
        return $this->searchRecursive($node->right, $data);
    }
}
```

### Usage:

```php
$result = $bint->search(60);
if($result){
    echo "Found: " . $result->data . " (count: " . $result->count . ")";
} else {
    echo "Not found";
}
```

---

# ✅ 2. Delete in BST

Deletion has **3 cases**:

1. Node has **no child** → remove directly
2. Node has **one child** → replace with child
3. Node has **two children** → replace with inorder successor (smallest in right subtree)

---

### Delete Code

```php
public function delete($data){
    $this->root = $this->deleteRecursive($this->root, $data);
}

private function deleteRecursive($node, $data){
    if($node === null){
        return null;
    }

    if($data < $node->data){
        $node->left = $this->deleteRecursive($node->left, $data);
    } elseif($data > $node->data){
        $node->right = $this->deleteRecursive($node->right, $data);
    } else {
        // Found node

        // Handle duplicates
        if($node->count > 1){
            $node->count--;
            return $node;
        }

        // Case 1: No child
        if($node->left === null && $node->right === null){
            return null;
        }

        // Case 2: One child
        if($node->left === null){
            return $node->right;
        }
        if($node->right === null){
            return $node->left;
        }

        // Case 3: Two children
        $minNode = $this->findMin($node->right);
        $node->data = $minNode->data;
        $node->count = $minNode->count;

        $node->right = $this->deleteRecursive($node->right, $minNode->data);
    }

    return $node;
}

private function findMin($node){
    while($node->left !== null){
        $node = $node->left;
    }
    return $node;
}
```

---

# ✅ 3. Sorting the Data (Traversal)

BST gives **sorted data using Inorder Traversal**.

### Inorder = Left → Root → Right

👉 Output will be **ascending order**

```php
public function inorder(){
    $this->inorderRecursive($this->root);
}

private function inorderRecursive($node){
    if($node === null){
        return;
    }

    $this->inorderRecursive($node->left);

    // Print duplicates using count
    for($i = 0; $i < $node->count; $i++){
        echo $node->data . " ";
    }

    $this->inorderRecursive($node->right);
}
```

---

### Example

```php
$bint->create(50);
$bint->create(60);
$bint->create(40);
$bint->create(70);
$bint->create(60);

$bint->inorder();
```

### Output:

```
40 50 60 60 70
```

✔ Sorted automatically

---

# 🔥 Quick Summary

| Operation | Method                          |
| --------- | ------------------------------- |
| Search    | Compare and traverse left/right |
| Delete    | Handle 3 cases (0,1,2 children) |
| Sort      | Inorder traversal               |

---

If you want next level improvements, I can show:

* **Iterative (non-recursive) versions**
* **Balancing (AVL / Red-Black Tree)**
* **Level-order traversal (BFS)**

Just tell me 👍
///////////////////////============================================
///////////////////////============================================
///////////////////////============================================
///////////////////////============================================
///////////////////////============================================
///////////////////////============================================